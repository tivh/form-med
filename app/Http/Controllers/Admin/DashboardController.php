<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (!$user->isSuperAdmin()) {
            $defaultRoute = config("admin_areas.{$user->form_scope}.default_route");

            abort_unless($defaultRoute, 403, 'Nenhuma área configurada para este usuário.');

            return redirect()->route($defaultRoute);
        }

        $areas = collect(config('admin_areas'))->map(function ($area, $slug) {
            return [
                'slug' => $slug,
                'label' => $area['label'],
                'title' => $area['dashboard_title'],
                'route' => $area['default_route'],
                'count' => $area['count_model']::count(),
            ];
        })->values();

        return view('admin.dashboard', ['areas' => $areas]);
    }
}