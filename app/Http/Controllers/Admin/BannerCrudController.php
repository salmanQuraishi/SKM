<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use App\Services\FileUploadService;

class BannerCrudController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('position')->orderBy('sort_order')->paginate(15);
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'button_text' => 'required|string|max:100',
            'button_url' => 'required|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'car_image' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = FileUploadService::upload($request->file('image'), 'banners');
        }

        $carImagePath = null;
        if ($request->hasFile('car_image')) {
            $carImagePath = FileUploadService::upload($request->file('car_image'), 'banners');
        }

        $features = array_filter(array_map('trim', explode("\n", $request->features_text ?? '')));

        Banner::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'badge_text' => $request->badge_text,
            'image_path' => $imagePath,
            'car_image_path' => $carImagePath,
            'button_text' => $request->button_text,
            'button_url' => $request->button_url,
            'secondary_button_text' => $request->secondary_button_text,
            'secondary_button_url' => $request->secondary_button_url,
            'position' => $request->position ?? 'hero',
            'features_json' => !empty($features) ? array_values($features) : null,
            'status' => $request->boolean('status'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully!');
    }

    public function edit(int $id)
    {
        $banner = Banner::findOrFail($id);
        $featuresText = $banner->features_json ? implode("\n", $banner->features_json) : '';

        return view('admin.banners.edit', compact('banner', 'featuresText'));
    }

    public function update(Request $request, int $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'button_text' => 'required|string|max:100',
            'button_url' => 'required|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'car_image' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg|max:5120',
        ]);

        $imagePath = $banner->image_path;
        if ($request->hasFile('image')) {
            $imagePath = FileUploadService::upload($request->file('image'), 'banners');
        }

        $carImagePath = $banner->car_image_path;
        if ($request->hasFile('car_image')) {
            $carImagePath = FileUploadService::upload($request->file('car_image'), 'banners');
        }

        $features = array_filter(array_map('trim', explode("\n", $request->features_text ?? '')));

        $banner->update([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'badge_text' => $request->badge_text,
            'image_path' => $imagePath,
            'car_image_path' => $carImagePath,
            'button_text' => $request->button_text,
            'button_url' => $request->button_url,
            'secondary_button_text' => $request->secondary_button_text,
            'secondary_button_url' => $request->secondary_button_url,
            'position' => $request->position ?? 'hero',
            'features_json' => !empty($features) ? array_values($features) : null,
            'status' => $request->boolean('status'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully!');
    }

    public function destroy(int $id)
    {
        $banner = Banner::findOrFail($id);
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted.');
    }
}
