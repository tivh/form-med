<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GlpiFeedService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class GlpiFeedController extends Controller
{
    public function index(): View
    {
        return view('admin.glpi.feed');
    }

    public function data(GlpiFeedService $glpi): JsonResponse
    {
        return response()->json($glpi->fetchTickets());
    }
}
