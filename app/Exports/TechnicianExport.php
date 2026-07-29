<?php

namespace App\Exports;

use App\Models\Technician;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TechnicianExport
{
    public function download()
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Nama');
        $sheet->setCellValue('B1', 'NIK');
        $sheet->setCellValue('C1', 'Username');
        $sheet->setCellValue('D1', 'No HP');
        $sheet->setCellValue('E1', 'Email');
        $sheet->setCellValue('F1', 'Alamat');
        $sheet->setCellValue('G1', 'Jabatan');
        $sheet->setCellValue('H1', 'Team');
        $sheet->setCellValue('I1', 'Status');
        $sheet->setCellValue('J1', 'Tanggal Masuk');
        $sheet->setCellValue('K1', 'Keterangan');

        $row = 2;

        foreach (Technician::with('team')->orderBy('nama')->get() as $technician) {

            $sheet->setCellValue('A'.$row, $technician->nama);
            $sheet->setCellValue('B'.$row, $technician->nik);
            $sheet->setCellValue('C'.$row, $technician->username);
            $sheet->setCellValue('D'.$row, $technician->telepon);
            $sheet->setCellValue('E'.$row, $technician->email);
            $sheet->setCellValue('F'.$row, $technician->alamat);
            $sheet->setCellValue('G'.$row, $technician->jabatan);
            $sheet->setCellValue('H'.$row, optional($technician->team)->nama);
            $sheet->setCellValue('I'.$row, $technician->status ? 'Aktif' : 'Nonaktif');
            $sheet->setCellValue('J'.$row, $technician->tanggal_masuk);
            $sheet->setCellValue('K'.$row, $technician->keterangan);

            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        $filename = 'Master_Teknisi_'.date('Y-m-d_H-i-s').'.xlsx';

        $tempFile = tempnam(sys_get_temp_dir(), 'technician');

        $writer->save($tempFile);

        return response()
            ->download($tempFile, $filename)
            ->deleteFileAfterSend(true);
    }
}