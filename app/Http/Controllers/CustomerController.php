<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->get();

        return view('customers.index', compact('customers'));
    }
    public function create()
    {
        return view('customers.create');
    }
    public function store(Request $request)
    {
    Customer::create($request->all());

    return redirect()
        ->route('customers.index')
        ->with('success', 'Data pelanggan berhasil disimpan.');
    }
}