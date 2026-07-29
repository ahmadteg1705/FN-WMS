<?php

namespace App\Http\Controllers;

use App\Models\WorkOrderAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WorkOrderAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'work_order_id' => ['required', 'exists:work_orders,id'],
        'username' => ['required', 'string', 'max:100', 'unique:work_order_accounts,username'],
        'password' => ['required', 'string', 'max:100'],
    ]);

    WorkOrderAccount::create([
        'work_order_id' => $validated['work_order_id'],
        'username'      => $validated['username'],
        'password'      => $validated['password'],
        'created_by'    => Auth::id(),
        'updated_by'    => Auth::id(),
    ]);

    return redirect()
        ->back()
        ->with('success', 'Akun PPPoE berhasil disimpan.');
}

    /**
     * Display the specified resource.
     */
    public function show(WorkOrderAccount $workOrderAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkOrderAccount $workOrderAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkOrderAccount $workOrderAccount)
{
    $validated = $request->validate([
        'username' => [
            'required',
            'string',
            'max:100',
            Rule::unique('work_order_accounts')
                ->ignore($workOrderAccount->id),
        ],
        'password' => ['required', 'string', 'max:100'],
    ]);

    $workOrderAccount->update([
        'username'   => $validated['username'],
        'password'   => $validated['password'],
        'updated_by' => Auth::id(),
    ]);

    return redirect()
        ->back()
        ->with('success', 'Akun PPPoE berhasil diperbarui.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkOrderAccount $workOrderAccount)
    {
        //
    }
}
