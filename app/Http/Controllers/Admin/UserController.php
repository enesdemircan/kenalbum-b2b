<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Customer;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles', 'customer');
        
        // Ad'a göre filtreleme
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        
        // Email'e göre filtreleme
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        
        // Role göre filtreleme
        if ($request->filled('role_id')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('roles.id', $request->role_id);
            });
        }
        
        // Firma'ya göre filtreleme
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        
        // Status'a göre filtreleme
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $users = $query->orderBy('id', 'desc')->paginate(15);
        $roles = Role::all();
        $customers = Customer::all();
        
        // Filtre parametrelerini view'a gönder
        $filters = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'customer_id' => $request->customer_id,
            'status' => $request->status
        ];
        
        return view('admin.users.index', compact('users', 'roles', 'customers', 'filters'));
    }

    public function create()
    {
        $roles = Role::all();
        $customers = Customer::all();
        return view('admin.users.create', compact('roles', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'customer_id' => 'nullable|exists:customers,id',
            'status' => 'required|in:0,1'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'customer_id' => $request->customer_id,
            'status' => $request->status
        ]);

        // Rolleri ata
        $user->roles()->attach($request->roles);

        return redirect()->route('admin.users.index')->with('success', 'Kullanıcı başarıyla oluşturuldu!');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id'
        ]);

        $user->roles()->sync($request->roles);
        return redirect()->back()->with('success', 'Kullanıcı rolleri güncellendi!');
    }

    public function updateCustomer(Request $request, User $user)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id'
        ]);

        $user->update(['customer_id' => $request->customer_id]);
        return redirect()->back()->with('success', 'Kullanıcı firma ataması güncellendi!');
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:0,1'
        ]);

        $user->update(['status' => $request->status]);
        
        $statusText = $request->status == 1 ? 'onaylandı' : 'onay bekliyor durumuna alındı';
        return redirect()->back()->with('success', "Kullanıcı {$statusText}!");
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $customers = Customer::all();
        return view('admin.users.edit', compact('user', 'roles', 'customers'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'customer_id' => 'nullable|exists:customers,id',
            'status' => 'required|in:0,1'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'customer_id' => $request->customer_id,
            'status' => $request->status
        ]);

        // Rolleri güncelle
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        } else {
            $user->roles()->detach();
        }

        return redirect()->route('admin.users.index')->with('success', 'Kullanıcı başarıyla güncellendi!');
    }

    public function destroy(User $user)
    {
        // Kullanıcının rolleri varsa sil
        $user->roles()->detach();
        
        // Kullanıcıyı sil
        $user->delete();
        
        return redirect()->route('admin.users.index')->with('success', 'Kullanıcı başarıyla silindi!');
    }
}
