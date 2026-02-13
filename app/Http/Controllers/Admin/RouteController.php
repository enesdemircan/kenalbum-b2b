<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $routes = Route::with('roles')
            ->orderBy('group')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.routes.index', compact('routes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = Route::distinct()->pluck('group')->filter();
        $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
        
        return view('admin.routes.create', compact('groups', 'methods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:routes,name',
            'uri' => 'required|string',
            'method' => 'required|in:GET,POST,PUT,PATCH,DELETE',
            'group' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        Route::create($request->all());

        return redirect()->route('admin.routes.index')
            ->with('success', 'Route başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Route $route)
    {
        $route->load('roles');
        return view('admin.routes.show', compact('route'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Route $route)
    {
        $groups = Route::distinct()->pluck('group')->filter();
        $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
        
        return view('admin.routes.edit', compact('route', 'groups', 'methods'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Route $route)
    {
        $request->validate([
            'name' => 'required|string|unique:routes,name,' . $route->id,
            'uri' => 'required|string',
            'method' => 'required|in:GET,POST,PUT,PATCH,DELETE',
            'group' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $route->update($request->all());

        return redirect()->route('admin.routes.index')
            ->with('success', 'Route başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Route $route)
    {
        $route->delete();

        return redirect()->route('admin.routes.index')
            ->with('success', 'Route başarıyla silindi.');
    }

    /**
     * Mevcut route'ları otomatik olarak import et
     */
    public function importRoutes()
    {
        try {
            // Artisan command ile route'ları import et
            $output = Artisan::call('route:import', [
                '--group' => 'admin',
                '--force' => true
            ]);
            
            return redirect()->route('admin.routes.index')
                ->with('success', 'Route\'lar başarıyla import edildi.');
        } catch (\Exception $e) {
            return redirect()->route('admin.routes.index')
                ->with('error', 'Route import hatası: ' . $e->getMessage());
        }
    }

    /**
     * Route'ları gruplara göre filtrele
     */
    public function byGroup($group)
    {
        $routes = Route::where('group', $group)
            ->with('roles')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.routes.index', compact('routes', 'group'));
    }

    /**
     * Route'ları aktif/pasif yap
     */
    public function toggleStatus(Route $route)
    {
        $route->update(['is_active' => !$route->is_active]);

        return redirect()->back()
            ->with('success', 'Route durumu güncellendi.');
    }

    /**
     * Route'a rol ata
     */
    public function assignRole(Request $request, Route $route)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'array'
        ]);

        $permissions = $request->input('permissions', []);
        
        // Varsayılan izinleri ayarla
        $defaultPermissions = [
            'can_access' => in_array('can_access', array_keys($permissions)),
            'can_create' => in_array('can_create', array_keys($permissions)),
            'can_read' => in_array('can_read', array_keys($permissions)),
            'can_update' => in_array('can_update', array_keys($permissions)),
            'can_delete' => in_array('can_delete', array_keys($permissions))
        ];

        $route->roles()->syncWithoutDetaching([
            $request->role_id => $defaultPermissions
        ]);

        return redirect()->back()
            ->with('success', 'Rol başarıyla atandı.');
    }

    /**
     * Route'dan rol kaldır
     */
    public function removeRole(Route $route, \App\Models\Role $role)
    {
        $route->roles()->detach($role->id);

        return redirect()->back()
            ->with('success', 'Rol başarıyla kaldırıldı.');
    }

    /**
     * Rol izinlerini güncelle
     */
    public function updateRolePermissions(Request $request, Route $route, \App\Models\Role $role)
    {
        $request->validate([
            'permissions' => 'array'
        ]);

        $permissions = $request->input('permissions', []);
        
        $updatedPermissions = [
            'can_access' => in_array('can_access', array_keys($permissions)),
            'can_create' => in_array('can_create', array_keys($permissions)),
            'can_read' => in_array('can_read', array_keys($permissions)),
            'can_update' => in_array('can_update', array_keys($permissions)),
            'can_delete' => in_array('can_delete', array_keys($permissions))
        ];

        $route->roles()->updateExistingPivot($role->id, $updatedPermissions);

        return redirect()->back()
            ->with('success', 'Rol izinleri güncellendi.');
    }
}
