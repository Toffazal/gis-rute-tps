<?php

namespace App\Services;

use App\Models\Jadwal;
use App\Models\User;
use App\Models\Tps;
use App\Models\Angkutan;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\UploadedFile;
use Exception;

class JadwalExcelService
{
    /**
     * Get style array for header (Bold, Yellow Background, Thin Border).
     */
    private function getHeaderStyle()
    {
        return [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'FFFF00', // Yellow
                ],
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
    }

    /**
     * Export jadwal to spreadsheet.
     */
    public function export($filterHari = null)
    {
        $query = Jadwal::with(['user', 'tps', 'angkutan']);
        if ($filterHari) {
            $query->where('hari', $filterHari);
        }
        $jadwalList = $query->orderBy('id', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Jadwal Pengangkutan');

        // Headers (A1:D1)
        $sheet->setCellValue('A1', 'Hari');
        $sheet->setCellValue('B1', 'Nama Sopir');
        $sheet->setCellValue('C1', 'Nama Angkutan');
        $sheet->setCellValue('D1', 'Nama TPS');

        // Apply styles
        $sheet->getStyle('A1:D1')->applyFromArray($this->getHeaderStyle());

        $row = 2;
        foreach ($jadwalList as $jadwal) {
            $sheet->setCellValue('A' . $row, $jadwal->hari);
            $sheet->setCellValue('B' . $row, $jadwal->user->name ?? '');
            $sheet->setCellValue('C' . $row, $jadwal->angkutan->nama ?? '');
            $sheet->setCellValue('D' . $row, $jadwal->tps->nama ?? '');
            $row++;
        }

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Download import template spreadsheet.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Jadwal');

        // Headers
        $sheet->setCellValue('A1', 'Hari');
        $sheet->setCellValue('B1', 'Nama Sopir');
        $sheet->setCellValue('C1', 'Nama Angkutan');
        $sheet->setCellValue('D1', 'Nama TPS');

        // Apply styles
        $sheet->getStyle('A1:D1')->applyFromArray($this->getHeaderStyle());

        // Put example data (Sopir name, Angkutan name, and TPS name if available in database, otherwise placeholders)
        $sampleSopir = User::where('role', 'sopir')->first();
        $sampleAngkutan = Angkutan::first();
        $sampleTps = Tps::first();

        $sheet->setCellValue('A2', 'Senin');
        $sheet->setCellValue('B2', $sampleSopir ? $sampleSopir->name : 'Nama Sopir Contoh');
        $sheet->setCellValue('C2', $sampleAngkutan ? $sampleAngkutan->nama : 'Nama Angkutan Contoh');
        $sheet->setCellValue('D2', $sampleTps ? $sampleTps->nama : 'Nama TPS Contoh');

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Import jadwal from spreadsheet.
     */
    public function import(UploadedFile $file)
    {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getRealPath());
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        $importedCount = 0;
        $errors = [];

        // Cache references for fast lookups (by trimmed, lowercase names)
        $sopirMap = User::where('role', 'sopir')->get()->keyBy(function($user) {
            return strtolower(trim($user->name));
        });
        $tpsMap = Tps::get()->keyBy(function($tps) {
            return strtolower(trim($tps->nama));
        });
        $angkutanMap = Angkutan::get()->keyBy(function($ang) {
            return strtolower(trim($ang->nama));
        });

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        for ($row = 2; $row <= $highestRow; $row++) {
            $hari = trim($sheet->getCell('A' . $row)->getValue() ?? '');
            $sopirName = trim($sheet->getCell('B' . $row)->getValue() ?? '');
            $angkutanName = trim($sheet->getCell('C' . $row)->getValue() ?? '');
            $tpsName = trim($sheet->getCell('D' . $row)->getValue() ?? '');

            // Skip completely empty rows
            if ($hari === '' && $sopirName === '' && $angkutanName === '' && $tpsName === '') {
                continue;
            }

            $rowErrors = [];

            if ($hari === '') {
                $rowErrors[] = "Hari kosong.";
            } elseif (!in_array($hari, $hariList)) {
                $rowErrors[] = "Hari '{$hari}' tidak valid. Harus salah satu dari: " . implode(', ', $hariList);
            }

            $sopirKey = strtolower($sopirName);
            if ($sopirName === '') {
                $rowErrors[] = "Nama Sopir kosong.";
            } elseif (!isset($sopirMap[$sopirKey])) {
                $rowErrors[] = "Sopir dengan nama '{$sopirName}' tidak ditemukan.";
            }

            $angkutanKey = strtolower($angkutanName);
            if ($angkutanName === '') {
                $rowErrors[] = "Nama Angkutan kosong.";
            } elseif (!isset($angkutanMap[$angkutanKey])) {
                $rowErrors[] = "Angkutan dengan nama '{$angkutanName}' tidak ditemukan.";
            }

            $tpsKey = strtolower($tpsName);
            if ($tpsName === '') {
                $rowErrors[] = "Nama TPS kosong.";
            } elseif (!isset($tpsMap[$tpsKey])) {
                $rowErrors[] = "TPS dengan nama '{$tpsName}' tidak ditemukan.";
            }

            if (!empty($rowErrors)) {
                $errors[] = "Baris {$row}: " . implode(' ', $rowErrors);
                continue;
            }

            Jadwal::create([
                'user_id' => $sopirMap[$sopirKey]->id,
                'tps_id' => $tpsMap[$tpsKey]->id,
                'angkutan_id' => $angkutanMap[$angkutanKey]->id,
                'hari' => $hari,
            ]);

            $importedCount++;
        }

        if (!empty($errors)) {
            throw new Exception("Sebagian data gagal di-import:\n" . implode("\n", $errors));
        }

        return $importedCount;
    }
}
