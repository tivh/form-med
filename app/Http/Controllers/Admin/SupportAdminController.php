<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportRequest;
use App\Services\GlpiSupportSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SupportAdminController extends Controller
{
    public function feed(): View
    {
        $requests = SupportRequest::with(['messages', 'user'])
            ->orderByDesc('updated_at')
            ->get();

        return view('admin.support.feed', compact('requests'));
    }

    public function show(SupportRequest $supportRequest): View
    {
        $supportRequest->load(['messages', 'user']);

        return view('admin.support.show', compact('supportRequest'));
    }

    public function reply(Request $request, SupportRequest $supportRequest, GlpiSupportSyncService $glpiSync)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $supportRequest->messages()->create([
            'sender_type' => 'admin',
            'sender_name' => Auth::user()?->name ?? 'Admin',
            'message' => $validated['message'],
        ]);

        $supportRequest->update([
            'status' => 'in_progress',
            'updated_at' => now(),
        ]);

        $glpiSync->syncReply($supportRequest, $validated['message']);

        return back()->with('success', 'Resposta enviada com sucesso.');
    }

    public function close(SupportRequest $supportRequest, GlpiSupportSyncService $glpiSync)
    {
        $supportRequest->update([
            'status' => 'closed',
        ]);

        $supportRequest->messages()->create([
            'sender_type' => 'admin',
            'sender_name' => Auth::user()?->name ?? 'Admin',
            'message' => 'Solicitação concluída pelo atendimento.',
        ]);

        $glpiSync->syncClose($supportRequest);

        return back()->with('success', 'Solicitação concluída.');
    }
}
