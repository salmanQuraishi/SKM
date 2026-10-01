<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInquiry;
use Illuminate\Http\Request;

class ContactInquiryController extends Controller
{
    public function index()
    {
        $inquiries = ContactInquiry::latest()->paginate(20);
        return view('admin.inquiries.index', compact('inquiries'));
    }

    public function markAsRead(int $id)
    {
        $inquiry = ContactInquiry::findOrFail($id);
        $inquiry->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Marked as read.');
    }

    public function destroy(int $id)
    {
        $inquiry = ContactInquiry::findOrFail($id);
        $inquiry->delete();

        return redirect()->back()->with('success', 'Inquiry removed.');
    }
}
