<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\NocActivation;
use App\Models\Registration;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $selectedDate = $request->filled('date')
            ? Carbon::parse($request->input('date'))->toDateString()
            : now()->toDateString();
        $status = trim((string) $request->query('status', ''));

        $isAdmin = $user->hasAnyRole(['Super User','Super Admin','Admin','Administrator','Owner']);
        $mode = $isAdmin ? 'administrator' : ($user->hasRole('Teknisi') ? 'technician' : ($user->hasRole('Marketing') ? 'marketing' : ($user->hasRole('NOC') ? 'noc' : 'limited')));

        $summary = ['customers'=>0,'active_customers'=>0,'today_total'=>0,'today_completed'=>0,'today_pending'=>0];
        $items = collect();
        $statuses = collect();

        if ($mode === 'administrator' || $mode === 'technician') {
            $query = WorkOrder::query()->with(['registration','team','technician.user'])->whereDate('tanggal', $selectedDate);
            if ($mode === 'technician') {
                $technician = $user->technician;
                $query->when($technician, function ($q) use ($technician) {
                    $q->where(function ($x) use ($technician) {
                        $x->where('technician_id', $technician->id);
                        if ($technician->team_id) $x->orWhere('team_id', $technician->team_id);
                    });
                }, fn($q) => $q->whereRaw('1=0'));
            }
            $base = clone $query;
            if ($status !== '') $query->where('status', $status);
            $items = $query->orderBy('jam')->get();
            $statuses = (clone $base)->whereNotNull('status')->distinct()->orderBy('status')->pluck('status');
            $summary['today_total'] = (clone $base)->count();
            $summary['today_completed'] = (clone $base)->where('status','Selesai')->count();
            $summary['today_pending'] = max(0, $summary['today_total']-$summary['today_completed']);
            if ($mode === 'administrator') {
                $summary['customers'] = Customer::count();
                $summary['active_customers'] = Customer::where('status','Aktif')->count();
            }
        } elseif ($mode === 'marketing') {
            $marketingId = $user->marketing?->id;
            $query = Registration::query()->with(['package','odp','workOrder'])->whereDate('created_at',$selectedDate)
                ->when($marketingId, fn($q)=>$q->where('marketing_id',$marketingId), fn($q)=>$q->whereRaw('1=0'));
            $base = clone $query;
            if ($status !== '') $query->where('status',$status);
            $items = $query->latest()->get();
            $statuses = (clone $base)->whereNotNull('status')->distinct()->orderBy('status')->pluck('status');
            $summary['today_total'] = (clone $base)->count();
            $summary['today_completed'] = (clone $base)->whereIn('status',['Selesai','Pelanggan Aktif','Aktif'])->count();
            $summary['today_pending'] = max(0,$summary['today_total']-$summary['today_completed']);
        } elseif ($mode === 'noc') {
            $query = NocActivation::query()->with(['handler','workOrder.registration'])->whereDate('created_at',$selectedDate)
                ->where(fn($q)=>$q->where('handled_by',$user->id)->orWhere('status',NocActivation::STATUS_WAITING));
            $base = clone $query;
            if ($status !== '') $query->where('status',$status);
            $items = $query->latest()->get();
            $statuses = (clone $base)->whereNotNull('status')->distinct()->orderBy('status')->pluck('status');
            $summary['today_total'] = (clone $base)->count();
            $summary['today_completed'] = (clone $base)->where('status',NocActivation::STATUS_SUCCESS)->count();
            $summary['today_pending'] = max(0,$summary['today_total']-$summary['today_completed']);
        }

        return view('dashboard', compact('mode','selectedDate','status','statuses','summary','items'));
    }
}
