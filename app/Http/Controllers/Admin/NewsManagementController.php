<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsArticle;
use Illuminate\Http\Request;

class NewsManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsArticle::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('tag')) {
            $query->where('tag', $request->tag);
        }

        $news = $query->latest('published_at')->paginate(30);

        $sources = NewsArticle::select('source')->distinct()->orderBy('source')->pluck('source');
        $tags = NewsArticle::select('tag')->distinct()->orderBy('tag')->pluck('tag');

        return view('admin.news.index', compact('news', 'sources', 'tags'));
    }

    public function destroy(NewsArticle $news)
    {
        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'Tin tức đã được xóa thành công!');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:news_articles,id'],
        ]);

        NewsArticle::whereIn('id', $validated['ids'])->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'Đã xóa ' . count($validated['ids']) . ' tin tức!');
    }
}
