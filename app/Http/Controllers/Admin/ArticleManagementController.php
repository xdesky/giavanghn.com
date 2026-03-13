<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalysisArticle;
use Illuminate\Http\Request;

class ArticleManagementController extends Controller
{
    /**
     * Display articles list
     */
    public function index(Request $request)
    {
        $query = AnalysisArticle::query();

        // Filter by trigger type
        if ($request->filled('trigger_type')) {
            $query->where('trigger_type', $request->trigger_type);
        }

        // Filter by published status
        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->whereNotNull('published_at');
            } else {
                $query->whereNull('published_at');
            }
        }

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('analysis_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('analysis_date', '<=', $request->date_to);
        }

        $articles = $query->latest('analysis_date')->paginate(20);

        return view('admin.articles.index', compact('articles'));
    }

    /**
     * Show article details
     */
    public function show(AnalysisArticle $article)
    {
        return view('admin.articles.show', compact('article'));
    }

    /**
     * Show article edit form
     */
    public function edit(AnalysisArticle $article)
    {
        return view('admin.articles.edit', compact('article'));
    }

    /**
     * Update article
     */
    public function update(Request $request, AnalysisArticle $article)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:500'],
            'summary' => ['nullable', 'string'],
            'content' => ['required', 'string'],
        ]);

        $article->update($validated);

        return redirect()->route('admin.articles.show', $article)
                        ->with('success', 'Bài viết đã được cập nhật thành công!');
    }

    /**
     * Toggle article published status
     */
    public function togglePublish(AnalysisArticle $article)
    {
        $article->update([
            'published_at' => $article->published_at ? null : now()
        ]);

        $status = $article->published_at ? 'đã xuất bản' : 'đã ẩn';

        return back()->with('success', "Bài viết {$status} thành công!");
    }

    /**
     * Delete article
     */
    public function destroy(AnalysisArticle $article)
    {
        // Delete thumbnail if exists
        if ($article->thumbnail_path) {
            \Storage::disk('public')->delete($article->thumbnail_path);
        }

        $article->delete();

        return redirect()->route('admin.articles.index')
                        ->with('success', 'Bài viết đã được xóa thành công!');
    }

    /**
     * Regenerate article (force regeneration)
     */
    public function regenerate(Request $request)
    {
        $validated = $request->validate([
            'trigger' => ['required', 'in:daily,change'],
            'date' => ['nullable', 'date'],
        ]);

        $date = $validated['date'] ?? now();

        \Artisan::call('generate:analysis-article', [
            '--trigger' => $validated['trigger'],
            '--force' => true,
        ]);

        return back()->with('success', 'Bài viết đã được tạo lại thành công!');
    }
}
