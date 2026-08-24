<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // 1. Se for Super Admin, exibe todas as áreas do sistema
        if ($user->isSuperAdmin()) {
            $areas = Area::active()->get();

            // Se ainda não houver áreas no banco, usa fallback do config
            if ($areas->isEmpty()) {
                $areasData = collect(config('admin_areas'))->map(function ($area, $slug) {
                    return [
                        'slug' => $slug,
                        'label' => $area['label'],
                        'title' => $area['dashboard_title'],
                        'route' => $area['default_route'],
                        'count' => class_exists($area['count_model']) ? $area['count_model']::count() : 0,
                    ];
                })->values();

                return view('admin.dashboard', ['areas' => $areasData]);
            }

            $areasData = $areas->map(function (Area $area) {
                $configArea = config("admin_areas.{$area->slug}");
                return [
                    'slug' => $area->slug,
                    'label' => $area->name,
                    'title' => $configArea['dashboard_title'] ?? $area->description ?? $area->name,
                    'route' => $area->default_route ?? $configArea['default_route'] ?? 'admin.dashboard',
                    'count' => $area->count,
                ];
            })->values();

            return view('admin.dashboard', ['areas' => $areasData]);
        }

        // 2. Para usuários normais, verifica as áreas acessíveis
        $accessibleAreas = $user->accessibleAreas();

        if ($accessibleAreas->isEmpty()) {
            // Fallback legado se usuário tiver form_scope antigo não migrado
            if (!empty($user->form_scope)) {
                $defaultRoute = config("admin_areas.{$user->form_scope}.default_route");
                if ($defaultRoute) {
                    return redirect()->route($defaultRoute);
                }
            }

            abort(403, 'Nenhuma área de acesso configurada para o seu usuário. Solicite acesso ao administrador.');
        }

        // 3. Se o usuário tiver apenas 1 área vinculada, redireciona diretamente para ela
        if ($accessibleAreas->count() === 1) {
            $singleArea = $accessibleAreas->first();
            $defaultRoute = $singleArea->default_route ?: config("admin_areas.{$singleArea->slug}.default_route");

            abort_unless($defaultRoute, 403, 'Rota não configurada para esta área.');

            return redirect()->route($defaultRoute);
        }

        // 4. Se o usuário tiver 2 ou mais áreas vinculadas (ex: Diretora Financeira com Financeiro + Compliance),
        // exibe o Dashboard Hub exibindo apenas as áreas permitidas para ela.
        $areasData = $accessibleAreas->map(function (Area $area) {
            $configArea = config("admin_areas.{$area->slug}");
            return [
                'slug' => $area->slug,
                'label' => $area->name,
                'title' => $configArea['dashboard_title'] ?? $area->description ?? $area->name,
                'route' => $area->default_route ?? $configArea['default_route'] ?? 'admin.dashboard',
                'count' => $area->count,
            ];
        })->values();

        return view('admin.dashboard', ['areas' => $areasData]);
    }
}