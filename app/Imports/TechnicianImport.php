<?php

namespace App\Imports;

use App\Models\Technician;
use App\Models\Team;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TechnicianImport
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

            if (empty($row[0])) {
                continue;
            }

            if (Technician::where('nik', trim($row[1]))->exists()) {

                $this->failed++;

                $this->errors[] = [

                    'nama' => trim($row[0]),

                    'pesan' => 'NIK sudah terdaftar.'

                ];

                continue;
            }

            if (Technician::where('username', trim($row[2]))->exists()) {

                $this->failed++;

                $this->errors[] = [

                    'nama' => trim($row[0]),

                    'pesan' => 'Username sudah digunakan.'

                ];

                continue;
            }

           $team = Team::where('nama', trim($row[7]))->first();

Technician::create([

    'nama'          => trim($row[0]),
    'nik'           => trim($row[1]),
    'username'      => trim($row[2]),

    // Password default
    'password'      => Hash::make('password123'),

    'telepon'       => trim($row[3]),
    'email'         => trim($row[4]),
    'alamat'        => trim($row[5]),
    'jabatan'       => trim($row[6]),
    'team_id'       => $team->id,

    'status'        => strtolower(trim($row[8])) == 'aktif',

    'tanggal_masuk' => $row[9],
    'keterangan'    => $row[10],

]);

            $this->success++;

        }
    }
}