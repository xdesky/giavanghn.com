<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SubscriberNotificationMail;
use App\Models\NotificationLog;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SubscriberManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscriber::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('active', $request->status === 'active');
        }

        if ($request->filled('market')) {
            $query->whereJsonContains('markets', $request->market);
        }

        $subscribers = $query->latest()->paginate(30);
        $totalActive = Subscriber::where('active', true)->count();
        $totalInactive = Subscriber::where('active', false)->count();

        return view('admin.subscribers.index', compact('subscribers', 'totalActive', 'totalInactive'));
    }

    public function toggleStatus(Subscriber $subscriber)
    {
        $subscriber->update(['active' => !$subscriber->active]);
        $status = $subscriber->active ? 'kích hoạt' : 'vô hiệu hóa';

        return back()->with('success', "Đã {$status} subscriber: {$subscriber->email}");
    }

    public function destroy(Subscriber $subscriber)
    {
        $email = $subscriber->email;
        $subscriber->delete();

        return back()->with('success', "Đã xóa subscriber: {$email}");
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:subscribers,id'],
        ]);

        Subscriber::whereIn('id', $validated['ids'])->delete();

        return back()->with('success', 'Đã xóa ' . count($validated['ids']) . ' subscriber.');
    }

    // ── Push notification ──

    public function pushForm()
    {
        $totalActive = Subscriber::where('active', true)->count();
        $logs = NotificationLog::with('sender')->latest()->paginate(20);

        return view('admin.subscribers.push', compact('totalActive', 'logs'));
    }

    public function pushSend(Request $request)
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:200'],
            'content' => ['required', 'string', 'max:10000'],
            'markets' => ['nullable', 'array'],
            'markets.*' => ['string'],
        ]);

        $query = Subscriber::where('active', true);

        if (!empty($validated['markets'])) {
            $query->where(function ($q) use ($validated) {
                foreach ($validated['markets'] as $market) {
                    $q->orWhereJsonContains('markets', $market);
                }
            });
        }

        $subscribers = $query->get();
        $sent = 0;
        $failed = 0;

        foreach ($subscribers as $subscriber) {
            try {
                Mail::to($subscriber->email)
                    ->send(new SubscriberNotificationMail(
                        $subscriber,
                        $validated['subject'],
                        $validated['content'],
                    ));

                $subscriber->update(['last_notified_at' => now()]);
                $sent++;
            } catch (\Throwable $e) {
                $failed++;
                report($e);
            }
        }

        NotificationLog::create([
            'subject' => $validated['subject'],
            'content' => $validated['content'],
            'markets' => $validated['markets'] ?? null,
            'total_sent' => $sent,
            'total_failed' => $failed,
            'sent_by' => auth()->id(),
            'sent_at' => now(),
        ]);

        return back()->with('success', "Đã gửi thành công {$sent} email" . ($failed ? ", thất bại {$failed}" : '') . '.');
    }

    public function pushShow(NotificationLog $log)
    {
        $log->load('sender');

        return view('admin.subscribers.push-show', compact('log'));
    }
}
