<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\ContactInquiry;
use App\Models\Setting;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = CmsPage::where('slug', $slug)->where('is_published', true)->firstOrFail();
        return view('frontend.page', compact('page'));
    }

    public function contact()
    {
        $settings = Setting::getAllGrouped();
        return view('frontend.contact', compact('settings'));
    }

    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|max:2000',
        ]);

        ContactInquiry::create($request->all());

        return redirect()->back()->with('success', 'Thank you! Your message has been sent successfully. Our support team will get back to you shortly.');
    }
}
