<?php

namespace App\Http\Controllers;

use App\Models\WorkOrderAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WorkOrderAccountController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'work_order_id' => [
                'required',
                'integer',
                'exists:work_orders,id',
                'unique:work_order_accounts,work_order_id',
            ],
            'username' => [
                'required',
                'string',
                'max:100',
                'unique:work_order_accounts,username',
            ],
            'password' => ['required', 'string', 'min:6', 'max:100'],
        ], [
            'work_order_id.unique' => 'Work Order ini sudah memiliki akun PPPoE. Gunakan tombol Edit Akun.',
            'username.unique' => 'Username PPPoE sudah digunakan.',
        ]);

        WorkOrderAccount::create([
            'work_order_id' => $validated['work_order_id'],
            'username' => trim($validated['username']),
            'password' => $validated['password'],
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Akun PPPoE berhasil disimpan.');
    }

    public function update(Request $request, WorkOrderAccount $workOrderAccount): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('work_order_accounts', 'username')->ignore($workOrderAccount->id),
            ],
            'password' => ['required', 'string', 'min:6', 'max:100'],
        ], [
            'username.unique' => 'Username PPPoE sudah digunakan.',
        ]);

        $workOrderAccount->update([
            'username' => trim($validated['username']),
            'password' => $validated['password'],
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Akun PPPoE berhasil diperbarui.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(
            $request->user()
                && $request->user()->hasAnyRole(['Super User', 'Super Admin', 'Admin']),
            403,
            'Hanya Super User, Super Admin, atau Admin yang dapat mengubah akun PPPoE.'
        );
    }
}
