<?php

namespace App\Imports;

use App\Models\Router;
use App\Models\Odp;
use PhpOffice\PhpSpreadsheet\IOFactory;

class OdpImport
{
    public int $success = 0;
public int $failed = 0;
public array $errors = [];
    public function import($file)
    {
        $spreadsheet = IOFactory::load($file);

        $sheet = $spreadsheet->getActiveSheet();
  
        $rows = $sheet->toArray();

        unset($rows[0]);

        foreach ($rows as $index => $row) {

    $excelRow = $index + 2;

            if(empty($row[0])){

                continue;

            }
        $router = Router::where('nama', trim($row[1]))->first();

if (!$router) {

    $this->failed++;

    $this->errors[] = [

        'baris' => $excelRow,

        'nama' => trim($row[0]),

        'pesan' => 'Router NAS tidak ditemukan.'

    ];

    continue;

}
            // Cek apakah Nama ODP sudah ada
if (Odp::where('nama', trim($row[0]))->exists()) {

    $this->failed++;

    $this->errors[] = [

        'baris' => $excelRow,

        'nama' => trim($row[0]),

        'pesan' => 'Nama ODP sudah terdaftar.'

    ];

    continue;

}
$latitude  = trim($row[5] ?? '');
$longitude = trim($row[6] ?? '');

// Validasi Latitude
if ($latitude !== '' && (!is_numeric($latitude) || $latitude < -90 || $latitude > 90)) {

    $this->failed++;

    $this->errors[] = [
        'baris' => $excelRow,
        'nama'  => trim($row[0]),
        'pesan' => 'Latitude tidak valid.'
    ];

    continue;
}

// Validasi Longitude
if ($longitude !== '' && (!is_numeric($longitude) || $longitude < -180 || $longitude > 180)) {

    $this->failed++;

    $this->errors[] = [
        'baris' => $excelRow,
        'nama'  => trim($row[0]),
        'pesan' => 'Longitude tidak valid.'
    ];

    continue;
}
// Simpan data baru
Odp::create([

    'nama'       => trim($row[0]),
    'router'     => trim($row[1]),
    'card'       => trim($row[2]),
    'onu_awal'   => $row[3],
    'onu_akhir'  => $row[4],

    'kapasitas'  => ($row[4] - $row[3]) + 1,

    'latitude'   => $latitude ?: null,
    'longitude'  => $longitude ?: null,

    'status'     => true,

]);

$this->success++;

        }
    }
}