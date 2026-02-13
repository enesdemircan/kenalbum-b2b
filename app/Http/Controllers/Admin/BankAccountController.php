<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBankAccountRequest;
use App\Http\Requests\UpdateBankAccountRequest;
use Illuminate\Http\Request;
use App\Models\BankAccount;

class BankAccountController extends Controller
{
    public function index()
    {
        $bankAccounts = BankAccount::orderBy('id')->get();
        return view('admin.bank_accounts.index', compact('bankAccounts'));
    }

    public function create()
    {
        return view('admin.bank_accounts.create');
    }

    public function store(StoreBankAccountRequest $request)
    {

        BankAccount::create([
            'bank_name' => $request->bank_name,
            'iban' => $request->iban,
            'account_name' => $request->account_name,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.bank-accounts.index')->with('success', 'Banka hesabı başarıyla oluşturuldu!');
    }

    public function edit($id)
    {
        $bankAccount = BankAccount::findOrFail($id);
        return view('admin.bank_accounts.edit', compact('bankAccount'));
    }

    public function update(UpdateBankAccountRequest $request, $id)
    {

        $bankAccount = BankAccount::findOrFail($id);
        $bankAccount->fill($request->all());
        $bankAccount->is_active = $request->has('is_active');
        $bankAccount->save();

        return redirect()->route('admin.bank-accounts.index')->with('success', 'Banka hesabı başarıyla güncellendi!');
    }

    public function destroy($id)
    {
        $bankAccount = BankAccount::findOrFail($id);
        $bankAccount->delete();

        return redirect()->route('admin.bank-accounts.index')->with('success', 'Banka hesabı başarıyla silindi!');
    }
} 