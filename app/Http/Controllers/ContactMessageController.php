<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function index(): View
    {
        abort_unless(isAdmin(), 403);

        return view('contact-messages.index', [
            'messages' => ContactMessage::latest()->paginate(15),
        ]);
    }

    public function show(ContactMessage $message): View
    {
        abort_unless(isAdmin(), 403);

        return view('contact-messages.show', [
            'message' => $message,
        ]);
    }
}
