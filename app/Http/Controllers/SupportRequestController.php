<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use App\Models\SupportRequest;
use App\Services\GlpiSupportSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportRequestController extends Controller
{
    public function store(Request $request, GlpiSupportSyncService $glpiSync): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $user = Auth::user();

        $supportRequest = SupportRequest::create([
            'user_id' => $user?->id,
            'requester_name' => $user?->name ?? 'Usuário',
            'requester_email' => $user?->email ?? 'sem-email@local',
            'subject' => $validated['subject'],
            'status' => 'new',
            'source' => 'web',
        ]);

        SupportMessage::create([
            'support_request_id' => $supportRequest->id,
            'sender_type' => 'user',
            'sender_name' => $user?->name ?? 'Usuário',
            'message' => $validated['message'],
        ]);

        $glpiSync->syncCreated($supportRequest);

        return back()->with('success', 'Sua solicitação foi enviada com sucesso.');
    }

    public function index()
    {
        $requests = SupportRequest::query()
            ->where('user_id', auth()->id())
            ->with(['messages'])
            ->orderByDesc('updated_at')
            ->get();

        return view('support.index', compact('requests'));
    }

    public function show(SupportRequest $supportRequest)
    {
        abort_unless($supportRequest->user_id === auth()->id(), 403);

        $supportRequest->load(['messages']);

        return view('support.show', compact('supportRequest'));
    }

    public function create(Request $request)
    {
        $request->user();

        return view('support.create');
    }
}
