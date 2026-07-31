<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));
        $nas = trim((string) $request->query('nas', ''));
        $odp = trim((string) $request->query('odp', ''));
        $paket = trim((string) $request->query('paket', ''));

        $customers = Customer::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('nama', 'like', "%{$search}%")
                        ->orWhere('nomor_pelanggan', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%")
                        ->orWhere('telepon', 'like', "%{$search}%")
                        ->orWhere('sn_modem', 'like', "%{$search}%")
                        ->orWhere('pppoe_username', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($nas !== '', fn ($query) => $query->where('nas', $nas))
            ->when($odp !== '', fn ($query) => $query->where('odp', $odp))
            ->when($paket !== '', fn ($query) => $query->where('paket', $paket))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $filters = [
            'statuses' => Customer::query()->whereNotNull('status')->distinct()->orderBy('status')->pluck('status'),
            'nasList' => Customer::query()->whereNotNull('nas')->where('nas', '<>', '')->distinct()->orderBy('nas')->pluck('nas'),
            'odpList' => Customer::query()->whereNotNull('odp')->where('odp', '<>', '')->distinct()->orderBy('odp')->pluck('odp'),
            'packageList' => Customer::query()->whereNotNull('paket')->where('paket', '<>', '')->distinct()->orderBy('paket')->pluck('paket'),
        ];

        $statistics = [
            'total' => Customer::count(),
            'active' => Customer::where('status', 'Aktif')->count(),
            'inactive' => Customer::where('status', 'Nonaktif')->count(),
            'with_pppoe' => Customer::whereNotNull('pppoe_username')
                ->where('pppoe_username', '<>', '')
                ->count(),
        ];

        return view('customers.index', compact(
            'customers',
            'search',
            'status',
            'nas',
            'odp',
            'paket',
            'filters',
            'statistics'
        ));
    }

    public function create(): View
    {
        return view('customers.create', [
            'customer' => new Customer(),
            'suggestedCustomerNumber' => $this->generateNextCustomerNumber(),
        ]);
    }

    public function generateNumber(): JsonResponse
    {
        return response()->json([
            'nomor_pelanggan' => $this->generateNextCustomerNumber(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        DB::transaction(function () use ($request, &$validated, &$customer) {
            if (blank($validated['nomor_pelanggan'] ?? null)) {
                $validated['nomor_pelanggan'] = $this->generateNextCustomerNumber(true);
            }

            validator(
                ['nomor_pelanggan' => $validated['nomor_pelanggan']],
                ['nomor_pelanggan' => ['required', 'string', 'max:100', 'unique:customers,nomor_pelanggan']]
            )->validate();

            $validated = $this->storePhotos($request, $validated);
            $customer = Customer::create($validated);
        });

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Data pelanggan berhasil ditambahkan.');
    }

    public function show(Customer $customer): View
    {
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $this->validated($request, $customer);

        if (blank($validated['nomor_pelanggan'] ?? null)) {
            $validated['nomor_pelanggan'] = $customer->nomor_pelanggan;
        }

        $validated = $this->storePhotos($request, $validated, $customer);
        $customer->update($validated);

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $this->deletePhoto($customer->foto_ktp);
        $this->deletePhoto($customer->foto_rumah);
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Data pelanggan berhasil dihapus.');
    }

    public function export(Request $request): StreamedResponse
    {
        $fileName = 'pelanggan_' . now()->format('Ymd_His') . '.csv';

        $query = Customer::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = trim((string) $request->query('q'));
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('nama', 'like', "%{$search}%")
                        ->orWhere('nomor_pelanggan', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%")
                        ->orWhere('telepon', 'like', "%{$search}%")
                        ->orWhere('sn_modem', 'like', "%{$search}%")
                        ->orWhere('pppoe_username', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->query('status')))
            ->when($request->filled('nas'), fn ($query) => $query->where('nas', $request->query('nas')))
            ->when($request->filled('odp'), fn ($query) => $query->where('odp', $request->query('odp')))
            ->when($request->filled('paket'), fn ($query) => $query->where('paket', $request->query('paket')))
            ->orderBy('id');

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'nomor_pelanggan', 'nama', 'nik', 'alamat', 'telepon', 'email',
                'paket', 'odp', 'sn_modem', 'nas', 'onu_number',
                'pppoe_username', 'pppoe_password', 'latitude', 'longitude',
                'tanggal_registrasi', 'status', 'foto_ktp', 'foto_rumah', 'catatan',
            ]);

            $query->chunkById(500, function ($customers) use ($handle) {
                foreach ($customers as $customer) {
                    fputcsv($handle, [
                        $customer->nomor_pelanggan,
                        $customer->nama,
                        $customer->nik,
                        $customer->alamat,
                        $customer->telepon,
                        $customer->email,
                        $customer->paket,
                        $customer->odp,
                        $customer->sn_modem,
                        $customer->nas,
                        $customer->onu_number,
                        $customer->pppoe_username,
                        $customer->pppoe_password,
                        $customer->latitude,
                        $customer->longitude,
                        optional($customer->tanggal_registrasi)->format('Y-m-d') ?? $customer->tanggal_registrasi,
                        $customer->status,
                        $customer->foto_ktp,
                        $customer->foto_rumah,
                        $customer->catatan,
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function downloadTemplate(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'nomor_pelanggan', 'nama', 'nik', 'alamat', 'telepon', 'email',
                'paket', 'odp', 'sn_modem', 'nas', 'onu_number',
                'pppoe_username', 'pppoe_password', 'latitude', 'longitude',
                'tanggal_registrasi', 'status', 'catatan',
            ]);

            fputcsv($handle, [
                'FN-000001', 'Pelanggan Contoh', '3320000000000001',
                'Alamat lengkap pelanggan', '081234567890',
                'pelanggan@example.com', '20 Mbps', 'ODP-CONTOH-01',
                'ZTEA00000001', 'Router Jepara', '1', 'fn000001',
                'passwordcontoh', '-6.123456', '110.123456',
                now()->format('Y-m-d'), 'Aktif', 'Contoh data import',
            ]);

            fclose($handle);
        }, 'template_import_pelanggan.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');

        if ($handle === false) {
            return back()->withErrors(['file' => 'File import tidak dapat dibaca.']);
        }

        $header = fgetcsv($handle);

        if (!$header) {
            fclose($handle);
            return back()->withErrors(['file' => 'Header CSV tidak ditemukan.']);
        }

        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header[0]);
        $header = array_map(fn ($value) => trim(strtolower((string) $value)), $header);

        $requiredHeaders = ['nama', 'nik', 'alamat', 'telepon'];
        $missingHeaders = array_diff($requiredHeaders, $header);

        if ($missingHeaders !== []) {
            fclose($handle);
            return back()->withErrors([
                'file' => 'Kolom wajib tidak ditemukan: ' . implode(', ', $missingHeaders),
            ]);
        }

        $success = 0;
        $failed = 0;
        $errors = [];
        $line = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $line++;

            if (count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) {
                continue;
            }

            $row = array_pad($row, count($header), null);
            $data = array_combine($header, array_slice($row, 0, count($header)));

            try {
                validator($data, [
                    'nomor_pelanggan' => ['nullable', 'string', 'max:100'],
                    'nama' => ['required', 'string', 'max:150'],
                    'nik' => ['required', 'string', 'max:32'],
                    'alamat' => ['required', 'string'],
                    'telepon' => ['required', 'string', 'max:30'],
                    'email' => ['nullable', 'email', 'max:150'],
                    'tanggal_registrasi' => ['nullable', 'date'],
                    'status' => ['nullable', 'string', 'max:50'],
                ])->validate();

                $customerNumber = trim((string) ($data['nomor_pelanggan'] ?? ''));

                if ($customerNumber === '') {
                    $customerNumber = $this->generateNextCustomerNumber();
                }

                Customer::updateOrCreate(
                    ['nomor_pelanggan' => $customerNumber],
                    array_merge(
                        collect($data)
                            ->only((new Customer())->getFillable())
                            ->map(fn ($value) => $value === '' ? null : $value)
                            ->all(),
                        ['nomor_pelanggan' => $customerNumber]
                    )
                );

                $success++;
            } catch (\Throwable $exception) {
                $failed++;
                if (count($errors) < 10) {
                    $errors[] = "Baris {$line}: {$exception->getMessage()}";
                }
            }
        }

        fclose($handle);

        return redirect()
            ->route('customers.index')
            ->with('success', "Import selesai. Berhasil: {$success}, gagal: {$failed}.")
            ->with('import_errors', $errors);
    }

    private function validated(Request $request, ?Customer $customer = null): array
    {
        return $request->validate([
            'nomor_pelanggan' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('customers', 'nomor_pelanggan')->ignore($customer?->id),
            ],
            'nama' => ['required', 'string', 'max:150'],
            'nik' => [
                'required',
                'string',
                'max:32',
                Rule::unique('customers', 'nik')->ignore($customer?->id),
            ],
            'alamat' => ['required', 'string'],
            'telepon' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'paket' => ['nullable', 'string', 'max:150'],
            'odp' => ['nullable', 'string', 'max:150'],
            'sn_modem' => ['nullable', 'string', 'max:150'],
            'nas' => ['nullable', 'string', 'max:150'],
            'onu_number' => ['nullable', 'string', 'max:30'],
            'pppoe_username' => ['nullable', 'string', 'max:150'],
            'pppoe_password' => ['nullable', 'string', 'max:150'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'tanggal_registrasi' => ['nullable', 'date'],
            'status' => ['required', 'string', 'max:50'],
            'foto_ktp' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'foto_rumah' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function generateNextCustomerNumber(bool $lock = false): string
    {
        $query = Customer::query()
            ->whereNotNull('nomor_pelanggan')
            ->where('nomor_pelanggan', '<>', '');

        if ($lock) {
            $query->lockForUpdate();
        }

        $numbers = $query->pluck('nomor_pelanggan');

        $bestPrefix = 'FN-';
        $bestWidth = 6;
        $highest = 0;
        $latestPatternNumber = null;

        foreach ($numbers as $number) {
            $number = trim((string) $number);

            if (!preg_match('/^(.*?)(\d+)$/', $number, $matches)) {
                continue;
            }

            $prefix = $matches[1];
            $numericPart = $matches[2];
            $numericValue = (int) $numericPart;

            if ($numericValue >= $highest) {
                $highest = $numericValue;
                $bestPrefix = $prefix;
                $bestWidth = strlen($numericPart);
                $latestPatternNumber = $number;
            }
        }

        if ($latestPatternNumber === null) {
            return 'FN-000001';
        }

        do {
            $highest++;
            $candidate = $bestPrefix . str_pad((string) $highest, $bestWidth, '0', STR_PAD_LEFT);
        } while (Customer::where('nomor_pelanggan', $candidate)->exists());

        return $candidate;
    }

    private function storePhotos(
        Request $request,
        array $validated,
        ?Customer $customer = null
    ): array {
        foreach (['foto_ktp', 'foto_rumah'] as $field) {
            unset($validated[$field]);

            if (!$request->hasFile($field)) {
                continue;
            }

            if ($customer) {
                $this->deletePhoto($customer->{$field});
            }

            $validated[$field] = $request->file($field)
                ->store('customers/' . $field, 'public');
        }

        return $validated;
    }

    private function deletePhoto(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
