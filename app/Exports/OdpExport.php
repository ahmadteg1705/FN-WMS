<?php

namespace App\Exports;

use App\Models\Odp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OdpExport
{
    public function download()
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Nama ODP');
        $sheet->setCellValue('B1', 'Router');
        $sheet->setCellValue('C1', 'Card');
        $sheet->setCellValue('D1', 'ONU Awal');
        $sheet->setCellValue('E1', 'ONU Akhir');
        $sheet->setCellValue('F1', 'Kapasitas');
        $sheet->setCellValue('G1', 'Latitude');
        $sheet->setCellValue('H1', 'Longitude');
        $sheet->setCellValue('I1', 'Google Maps');
        $sheet->setCellValue('J1', 'Status');

        $row = 2;

        foreach (Odp::orderBy('nama')->get() as $odp) {

            $sheet->setCellValue('A'.$row, $odp->nama);
$sheet->setCellValue('B'.$row, $odp->router);
$sheet->setCellValue('C'.$row, $odp->card);
$sheet->setCellValue('D'.$row, $odp->onu_awal);
$sheet->setCellValue('E'.$row, $odp->onu_akhir);
$sheet->setCellValue('F'.$row, $odp->kapasitas);

$sheet->setCellValue('G'.$row, $odp->latitude);
$sheet->setCellValue('H'.$row, $odp->longitude);
$mapsUrl = '';

if ($odp->latitude && $odp->longitude) {

    $mapsUrl = 'https://www.google.com/maps?q='
        .$odp->latitude.','
        .$odp->longitude;

}

$sheet->setCellValue('I'.$row, $mapsUrl);
$sheet->setCellValue(
    'J'.$row,
    $odp->status ? 'Aktif' : 'Nonaktif'
);
if($mapsUrl){

    $sheet->getCell('I'.$row)
          ->getHyperlink()
          ->setUrl($mapsUrl);

}
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        $filename = 'ODP_'.date('Y-m-d_H-i-s').'.xlsx';

        $tempFile = tempnam(sys_get_temp_dir(), 'odp');

        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}