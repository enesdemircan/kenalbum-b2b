<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Customer::query();
        
        // Ünvan'a göre filtreleme
        if ($request->filled('unvan')) {
            $query->where('unvan', 'like', '%' . $request->unvan . '%');
        }
        
        // Telefon'a göre filtreleme
        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }
        
        // Email'e göre filtreleme
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        
        $customers = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Filtre parametrelerini view'a gönder
        $filters = [
            'unvan' => $request->unvan,
            'phone' => $request->phone,
            'email' => $request->email
        ];
        
        return view('admin.customers.index', compact('customers', 'filters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'firma_id' => 'required|unique:customers,firma_id',
            'unvan' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'adres' => 'required|string',
            'vergi_dairesi' => 'required|string|max:255',
            'vergi_numarasi' => 'required|string|max:50',
            'balance' => 'nullable|numeric|min:-999999999.99|max:999999999.99',
        ]);

        Customer::create($request->all());

        return redirect()->route('admin.customers.index')
            ->with('success', 'Firma başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'firma_id' => 'required|unique:customers,firma_id,' . $id,
            'unvan' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'adres' => 'required|string',
            'vergi_dairesi' => 'required|string|max:255',
            'vergi_numarasi' => 'required|string|max:50',
            'balance' => 'nullable|numeric|min:-999999999.99|max:999999999.99',
        ]);

        $customer->update($request->all());

        return redirect()->route('admin.customers.index')
            ->with('success', 'Firma başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Firma başarıyla silindi.');
    }

    /**
     * Get customers list for AJAX requests
     */
    public function getCustomersList(Request $request)
    {
        try {
            $customers = Customer::orderBy('unvan', 'asc')->get();
            
            return response()->json([
                'success' => true,
                'customers' => $customers
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Müşteri listesi alınırken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
