<?php

namespace App\Http\Controllers;
use App\Models\RegistrationHistory;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Package;
use App\Models\Odp;
use App\Models\Marketing;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index(Request $request)
{
    $query = Registration::with([
        'package',
        'odp',
        'marketing.user',
    ]);
    /*
|--------------------------------------------------------------------------
| Data Security
|--------------------------------------------------------------------------
*/

$user = auth()->user();

if ($user->hasRole('Marketing')) {

    if ($user->marketing) {

        $query->where('marketing_id', $user->marketing->id);

    } else {

        $query->whereRaw('1 = 0');

    }

}

    // Search
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('registration_number', 'like', '%' . $request->search . '%')
              ->orWhere('nama', 'like', '%' . $request->search . '%')
              ->orWhere('telepon', 'like', '%' . $request->search . '%');
        });
    }

    // Filter Status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Filter Marketing
    if ($request->filled('marketing_id')) {
        $query->where('marketing_id', $request->marketing_id);
    }

    // Filter Paket
    if ($request->filled('package_id')) {
        $query->where('package_id', $request->package_id);
    }

    // Filter Tanggal
    if ($request->filled('tanggal_dari')) {
        $query->whereDate('created_at', '>=', $request->tanggal_dari);
    }

    if ($request->filled('tanggal_sampai')) {
        $query->whereDate('created_at', '<=', $request->tanggal_sampai);
    }

    $registrations = $query
        ->latest()
        ->paginate(20)
        ->withQueryString();

    $marketings = Marketing::with('user')
        ->get()
        ->sortBy(fn ($marketing) => $marketing->user->name ?? '');

    $packages = Package::orderBy('nama')->get();

    return view(
        'registrations.index',
        compact(
            'registrations',
            'marketings',
            'packages'
        )
    );
}
    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    $packages = Package::where('status',1)
        ->orderBy('nama')
        ->get();

    $odps = Odp::orderBy('nama')
        ->get();
$marketings = Marketing::with('user')
    ->get()
    ->sortBy(fn ($marketing) => $marketing->user->name ?? '');
    return view(
        'registrations.create',
        compact('packages','odps','marketings')
    );
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'nama'       => 'required|max:255',
        'telepon'    => 'required|max:20',
        'foto_ktp' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'alamat'     => 'required',
        'package_id' => 'required',
        'odp_id'     => 'required',
    ]);

    $nomor =
        'REG-'
        . now()->format('Ymd')
        . '-'
        . str_pad(
            Registration::count()+1,
            4,
            '0',
            STR_PAD_LEFT
        );
$fotoKtp = null;

if ($request->hasFile('foto_ktp')) {

    $fotoKtp = $request
        ->file('foto_ktp')
        ->store('registrations/ktp', 'public');
}
    $registration = Registration::create([

        'registration_number' => $nomor,

        'nama' => $request->nama,
        'nik' => $request->nik,
        'telepon' => $request->telepon,
        'foto_ktp' => $fotoKtp,
        'alamat' => $request->alamat,

        'latitude' => $request->latitude,
        'longitude' => $request->longitude,

        'package_id' => $request->package_id,
        'odp_id' => $request->odp_id,

        'marketing_id' => $request->marketing_id,

        'status' => 'Registrasi Baru',

        'keterangan' => $request->keterangan,

    ]);
    RegistrationHistory::create([
    'registration_id' => $registration->id,
    'status_lama' => null,
    'status_baru' => $registration->status,
    'catatan' => 'Registrasi pelanggan dibuat.',
    'user_id' => auth()->id(),
]);

    return redirect()
        ->route('registrations.index')
        ->with('success','Registrasi berhasil disimpan.');
}

    /**
     * Display the specified resource.
     */
    public function show(Registration $registration)
{
    $registration->load([
        'package',
        'odp',
        'marketing.user',
        'histories.user'
    ]);

    return view('registrations.show', compact('registration'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Registration $registration)
{
    $packages = Package::where('status',1)
        ->orderBy('nama')
        ->get();

    $odps = Odp::orderBy('nama')
        ->get();
$marketings = Marketing::with('user')
    ->get()
    ->sortBy(fn ($marketing) => $marketing->user->name ?? '');
    return view(
        'registrations.edit',
        compact(
    'registration',
    'packages',
    'odps',
    'marketings'
)
    );
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Registration $registration)
{
    $request->validate([
        'nama'       => 'required|max:255',
        'telepon'    => 'required|max:20',
        'alamat'     => 'required',
        'package_id' => 'required|exists:packages,id',
        'odp_id'     => 'required|exists:odps,id',
        'foto_ktp'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $fotoKtp = $registration->foto_ktp;

    if ($request->hasFile('foto_ktp')) {

        if ($fotoKtp && Storage::disk('public')->exists($fotoKtp)) {
            Storage::disk('public')->delete($fotoKtp);
        }

        $fotoKtp = $request
            ->file('foto_ktp')
            ->store('registrations/ktp','public');
    }

    $registration->update([

        'nama' => $request->nama,
        'nik' => $request->nik,
        'telepon' => $request->telepon,
        'alamat' => $request->alamat,

        'latitude' => $request->latitude,
        'longitude' => $request->longitude,

        'package_id' => $request->package_id,
        'odp_id' => $request->odp_id,

        'marketing_id' => $request->marketing_id,

        'keterangan' => $request->keterangan,

        'foto_ktp' => $fotoKtp,

    ]);

    return redirect()
        ->route('registrations.index')
        ->with('success','Registrasi berhasil diperbarui.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Registration $registration)
{
    if (
        $registration->foto_ktp &&
        Storage::disk('public')->exists($registration->foto_ktp)
    ) {
        Storage::disk('public')->delete($registration->foto_ktp);
    }

    $registration->delete();

    return redirect()
        ->route('registrations.index')
        ->with('success','Registrasi berhasil dihapus.');
}
public function odpInfo(Odp $odp)
{
    $kapasitas = ($odp->onu_akhir - $odp->onu_awal) + 1;

    $terpakai = Registration::where('odp_id', $odp->id)
        ->where('status', '!=', 'Batal')
        ->count();

    $sisa = max(0, $kapasitas - $terpakai);

    if ($sisa <= 0) {

    $status = [
        'title'   => 'ODP Sudah Penuh',
        'message' => 'Semua port telah digunakan.',
        'color'   => 'red'
    ];

} elseif ($sisa <= 2) {

    $status = [
        'title'   => 'ODP Hampir Penuh',
        'message' => "Tersisa {$sisa} port.",
        'color'   => 'yellow'
    ];

} else {

    $status = [
        'title'   => 'ODP Masih Tersedia',
        'message' => "Masih tersedia {$sisa} port untuk digunakan.",
        'color'   => 'green'
    ];

}

return response()->json([
    'kapasitas' => $kapasitas,
    'terpakai'  => $terpakai,
    'sisa'      => $sisa,
    'status'    => $status,
]);
}
public function editStatus(Registration $registration)
{
    $statuses = [
        'Registrasi Baru',
        'Diverifikasi',
        'Dijadwalkan',
        'Proses Instalasi',
        'Aktivasi',
        'Pelanggan Aktif',
        'Batal',
    ];

    return view('registrations.edit-status', compact(
        'registration',
        'statuses'
    ));
}

public function updateStatus(Request $request, Registration $registration)
{
    $request->validate([
        'status' => 'required',
        'catatan' => 'nullable|string',
    ]);

    $statusLama = $registration->status;

    $registration->update([
        'status' => $request->status,
    ]);

    RegistrationHistory::create([
        'registration_id' => $registration->id,
        'status_lama' => $statusLama,
        'status_baru' => $request->status,
        'catatan' => $request->catatan,
        'user_id' => auth()->id(),
    ]);

    return redirect()
        ->route('registrations.show', $registration)
        ->with('success', 'Status berhasil diperbarui.');
}
public function verify(Registration $registration)
{
    if ($registration->status !== 'Registrasi Baru') {
        return back()->with(
            'error',
            'Registrasi ini sudah diverifikasi.'
        );
    }

    $statusLama = $registration->status;

    $registration->update([
        'status' => 'Diverifikasi',
    ]);

    RegistrationHistory::create([
        'registration_id' => $registration->id,
        'status_lama'     => $statusLama,
        'status_baru'     => 'Diverifikasi',
        'catatan'         => 'Registrasi telah diverifikasi oleh Admin.',
        'user_id'         => auth()->id(),
    ]);

    return redirect()
        ->route('registrations.show', $registration)
        ->with(
            'success',
            'Registrasi berhasil diverifikasi.'
        );
}
}
