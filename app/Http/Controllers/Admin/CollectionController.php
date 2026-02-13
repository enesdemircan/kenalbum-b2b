<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Customer;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Collection::with('customer');
        
        // Firma adına göre filtreleme
        if ($request->filled('customer_name')) {
            $query->whereHas('customer', function($q) use ($request) {
                $q->where('unvan', 'like', '%' . $request->customer_name . '%');
            });
        }
        
        // Tahsilat şekline göre filtreleme
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        // Tarihe göre filtreleme
        if ($request->filled('date_from')) {
            $query->whereDate('collection_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('collection_date', '<=', $request->date_to);
        }
        
        $collections = $query->orderBy('collection_date', 'desc')->paginate(15);
        
        // Filtre parametrelerini view'a gönder
        $filters = [
            'customer_name' => $request->customer_name,
            'payment_method' => $request->payment_method,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to
        ];
        
        return view('admin.collections.index', compact('collections', 'filters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::orderBy('unvan')->get();
        return view('admin.collections.create', compact('customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0.01|max:999999999.99',
            'payment_method' => 'required|in:kredi_karti,havale,nakit',
            'collection_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $collection = Collection::create($request->all());

        // Firmaya bakiye ekle
        $customer = Customer::find($request->customer_id);
        $customer->addBalance($request->amount);

        return redirect()->route('admin.collections.index')
            ->with('success', 'Tahsilat başarıyla oluşturuldu ve firmaya bakiye eklendi.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $collection = Collection::with('customer')->findOrFail($id);
        return view('admin.collections.show', compact('collection'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $collection = Collection::findOrFail($id);
        $customers = Customer::orderBy('unvan')->get();
        return view('admin.collections.edit', compact('collection', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $collection = Collection::findOrFail($id);
        $oldAmount = $collection->amount;

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0.01|max:999999999.99',
            'payment_method' => 'required|in:kredi_karti,havale,nakit',
            'collection_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Eski miktarı bakiyeden çıkar
        $oldCustomer = Customer::find($collection->customer_id);
        $oldCustomer->subtractBalance($oldAmount);

        // Yeni miktarı bakiyeye ekle
        $newCustomer = Customer::find($request->customer_id);
        $newCustomer->addBalance($request->amount);

        $collection->update($request->all());

        return redirect()->route('admin.collections.index')
            ->with('success', 'Tahsilat başarıyla güncellendi ve bakiye düzenlendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $collection = Collection::findOrFail($id);
        
        // Bakiyeden çıkar
        $customer = Customer::find($collection->customer_id);
        $customer->subtractBalance($collection->amount);
        
        $collection->delete();

        return redirect()->route('admin.collections.index')
            ->with('success', 'Tahsilat başarıyla silindi ve bakiyeden düşürüldü.');
    }

    /**
     * Firma bazında tahsilat listesi
     */
    public function customerCollections($customerId)
    {
        $customer = Customer::with('collections')->findOrFail($customerId);
        $collections = $customer->collections()->orderBy('collection_date', 'desc')->paginate(15);
        
        return view('admin.collections.customer', compact('customer', 'collections'));
    }

    /**
     * Belirli bir firma için tahsilat ekleme formu
     */
    public function createForCustomer($customerId)
    {
        $customer = Customer::findOrFail($customerId);
        return view('admin.collections.create-for-customer', compact('customer'));
    }

    /**
     * Belirli bir firma için tahsilat kaydetme
     */
    public function storeForCustomer(Request $request, $customerId)
    {
        $customer = Customer::findOrFail($customerId);
        
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:999999999.99',
            'payment_method' => 'required|in:kredi_karti,havale,nakit',
            'collection_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $collection = Collection::create([
            'customer_id' => $customerId,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'collection_date' => $request->collection_date,
            'notes' => $request->notes,
        ]);

        // Firmaya bakiye ekle
        $customer->addBalance($request->amount);

        return redirect()->route('admin.customers.collections', $customerId)
            ->with('success', 'Tahsilat başarıyla oluşturuldu ve firmaya bakiye eklendi.');
    }
} 