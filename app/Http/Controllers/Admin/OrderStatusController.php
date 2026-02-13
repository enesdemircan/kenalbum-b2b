<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderStatus;
use App\Models\Role;

class OrderStatusController extends Controller
{
    public function index()
    {
        $orderStatuses = OrderStatus::with('roles')->orderBy('id')->paginate(15);
        return view('admin.order_statuses.index', compact('orderStatuses'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.order_statuses.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:order_statuses,title',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $orderStatus = OrderStatus::create([
            'title' => $request->title,
        ]);

        // Rolleri ata
        if ($request->has('roles')) {
            $orderStatus->roles()->attach($request->roles);
        }

        return redirect()->route('admin.order-statuses.index')->with('success', 'Sipariş durumu başarıyla oluşturuldu!');
    }

    public function edit($id)
    {
        $orderStatus = OrderStatus::findOrFail($id);
        $roles = Role::orderBy('name')->get();
        return view('admin.order_statuses.edit', compact('orderStatus', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:order_statuses,title,' . $id,
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $orderStatus = OrderStatus::findOrFail($id);
        $orderStatus->update([
            'title' => $request->title,
        ]);

        // Rolleri güncelle
        $orderStatus->roles()->sync($request->roles ?? []);

        return redirect()->route('admin.order-statuses.index')->with('success', 'Sipariş durumu başarıyla güncellendi!');
    }

    public function destroy($id)
    {
        $orderStatus = OrderStatus::findOrFail($id);
        
        // Bu durumun kullanımda olup olmadığını kontrol et
        $usageCount = $orderStatus->orderStatusHistories()->count();
        
        if ($usageCount > 0) {
            return redirect()->route('admin.order-statuses.index')->with('error', 'Bu sipariş durumu kullanımda olduğu için silinemez!');
        }
        
        $orderStatus->delete();
        
        return redirect()->route('admin.order-statuses.index')->with('success', 'Sipariş durumu başarıyla silindi!');
    }
} 