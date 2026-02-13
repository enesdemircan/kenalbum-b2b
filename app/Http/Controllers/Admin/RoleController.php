<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('name')->paginate(15);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = [
            'users' => 'Kullanıcı Yönetimi',
            'products' => 'Ürün Yönetimi',
            'orders' => 'Sipariş Yönetimi',
            'categories' => 'Kategori Yönetimi',
            'customization' => 'Özelleştirme Yönetimi',
            'reports' => 'Raporlar',
            'settings' => 'Sistem Ayarları'
        ];
        
        // Route'ları gruplara göre getir
        $routeGroups = \App\Models\Route::select('group')
            ->whereNotNull('group')
            ->distinct()
            ->pluck('group')
            ->filter();
        
        $routes = \App\Models\Route::with('roles')
            ->orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy('group');
        
        return view('admin.roles.create', compact('permissions', 'routeGroups', 'routes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'routes' => 'nullable|array',
            'route_permissions' => 'nullable|array'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
            'permissions' => $request->permissions ?? []
        ]);

        // Route izinlerini ata
        if ($request->has('routes')) {
            foreach ($request->routes as $routeId) {
                $role->assignRoute($routeId);
            }
        }

        return redirect()->route('admin.roles.index')->with('success', 'Rol başarıyla oluşturuldu!');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = [
            'users' => 'Kullanıcı Yönetimi',
            'products' => 'Ürün Yönetimi',
            'orders' => 'Sipariş Yönetimi',
            'categories' => 'Kategori Yönetimi',
            'customization' => 'Özelleştirme Yönetimi',
            'reports' => 'Raporlar',
            'settings' => 'Sistem Ayarları'
        ];
        
        // Route'ları gruplara göre getir
        $routeGroups = \App\Models\Route::select('group')
            ->whereNotNull('group')
            ->distinct()
            ->pluck('group')
            ->filter();
        
        $routes = \App\Models\Route::with('roles')
            ->orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy('group');
        
        // Role'ün mevcut route izinlerini al
        $roleRoutes = $role->routes()->pluck('routes.id')->toArray();
        
        return view('admin.roles.edit', compact('role', 'permissions', 'routeGroups', 'routes', 'roleRoutes'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'routes' => 'nullable|array',
            'route_permissions' => 'nullable|array'
        ]);

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
            'permissions' => $request->permissions ?? []
        ]);

        // Önce tüm route izinlerini temizle
        $role->routes()->detach();

        // Route izinlerini ata
        if ($request->has('routes')) {
            foreach ($request->routes as $routeId) {
                $role->assignRoute($routeId);
            }
        }

        return redirect()->route('admin.roles.index')->with('success', 'Rol başarıyla güncellendi!');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        
        // Rolü kullanan kullanıcılar varsa silme
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')->with('error', 'Bu rolü kullanan kullanıcılar var. Önce kullanıcıların rollerini değiştirin.');
        }
        
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Rol başarıyla silindi!');
    }
}
