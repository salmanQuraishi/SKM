<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CmsPageCrudController extends Controller
{
    public function index()
    {
        $pages = CmsPage::latest()->paginate(15);
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'content' => 'required|string',
        ]);

        $slug = Str::slug($request->title);

        CmsPage::create([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'meta_title' => $request->meta_title ?: $request->title,
            'meta_description' => $request->meta_description,
            'is_published' => $request->boolean('is_published', true),
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'Page created successfully!');
    }

    public function edit(int $id)
    {
        $page = CmsPage::findOrFail($id);
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, int $id)
    {
        $page = CmsPage::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:200',
            'content' => 'required|string',
        ]);

        $page->update([
            'title' => $request->title,
            'content' => $request->content,
            'meta_title' => $request->meta_title ?: $request->title,
            'meta_description' => $request->meta_description,
            'is_published' => $request->boolean('is_published', true),
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'Page updated successfully!');
    }

    public function destroy(int $id)
    {
        $page = CmsPage::findOrFail($id);
        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Page deleted.');
    }
}
