<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Sortable;
use App\Models\ContactMessage;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    use Sortable;

    public function index(): View
    {
        abort_unless(isAdmin(), 403);

        $query = $this->applySort(
            ContactMessage::query(),
            sortable: ['name', 'topic', 'created_at'],
            defaultColumn: 'created_at',
            defaultDirection: 'desc',
        );

        return view('contact-messages.index', [
            'messages' => $query->paginate(15)->withQueryString(),
        ]);
    }

    public function show(ContactMessage $message): View
    {
        abort_unless(isAdmin(), 403);

        return view('contact-messages.show', [
            'message' => $message,
        ]);
    }

    public function destroy(ContactMessage $message)
    {
        abort_unless(isAdmin(), 403);

        $message->delete();

        return redirect()->route('messages.index')->with('success', 'Message deleted successfully.');
    }
}
