<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function registrationExcel(Request $request)
    {
        $query = Registration::with([
            'package',
            'odp',
            'marketing'
        ]);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%");
            });
        }

        // Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Marketing
        if ($request->filled('marketing')) {
            $query->where('marketing_id', $request->marketing);
        }

        // Paket
        if ($request->filled('package')) {
            $query->where('package_id', $request->package);
        }

        // Tanggal Awal
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        // Tanggal Akhir
        if ($request->filled('until_date')) {
            $query->whereDate('created_at', '<=', $request->until_date);
        }

        $registrations = $query
            ->orderBy('created_at', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Registrasi');

        $sheet->setCellValue('A1', 'FAHASA NET');
        $sheet->setCellValue('A2', 'LAPORAN REGISTRASI PELANGGAN');

        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');

        $sheet->fromArray([
            [
                'No',
                'No Registrasi',
                'Tanggal',
                'Nama',
                'Telepon',
                'Paket',
                'ODP',
                'Marketing',
                'Status'
            ]
        ], null, 'A4');

        $row = 5;
        $no = 1;

        foreach ($registrations as $item) {

            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $item->registration_number);
            $sheet->setCellValue("C{$row}",
                optional($item->created_at)?->format('d-m-Y H:i')
            );
            $sheet->setCellValue("D{$row}", $item->nama);
            $sheet->setCellValue("E{$row}", $item->telepon);
            $sheet->setCellValue("F{$row}", optional($item->package)->nama);
            $sheet->setCellValue("G{$row}", optional($item->odp)->nama);
            $sheet->setCellValue("H{$row}", optional($item->marketing)->nama);
            $sheet->setCellValue("I{$row}", $item->status);

            $row++;
        }

        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        $filename = 'Laporan_Registrasi_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }
}