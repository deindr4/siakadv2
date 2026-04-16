<?php
// app/Exports/AbsensiSiswaExport.php

namespace App\Exports;

use App\Models\Siswa;
use App\Models\Rombel;
use App\Models\AbsensiSiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AbsensiSiswaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Rekap Absensi Siswa';
    }

    public function collection()
    {
        $semesterId = $this->filters['semester_id'] ?? null;
        $rombelId   = $this->filters['rombel_id'] ?? null;

        $query = Siswa::when($rombelId, function ($q) use ($rombelId) {
                $rombel = Rombel::find($rombelId);
                if ($rombel) $q->where('rombongan_belajar_id', $rombel->rombongan_belajar_id);
            })
            ->when(!$rombelId && $semesterId, function ($q) use ($semesterId) {
                $uuids = Rombel::where('semester_id', $semesterId)->where('is_archived', false)->pluck('rombongan_belajar_id');
                $q->whereIn('rombongan_belajar_id', $uuids);
            })
            ->where(function ($q) { $q->where('is_archived', false)->orWhereNull('is_archived'); })
            ->orderBy('nama_rombel')->orderBy('nama');

        return $query->get()->map(function ($siswa) use ($semesterId) {
            $absensi = AbsensiSiswa::where('siswa_id', $siswa->id)
                ->whereHas('absensiHarian', fn($q) => $q->when($semesterId, fn($q) => $q->where('semester_id', $semesterId)))
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            $siswa->_h = $absensi['H'] ?? 0;
            $siswa->_s = $absensi['S'] ?? 0;
            $siswa->_i = $absensi['I'] ?? 0;
            $siswa->_a = $absensi['A'] ?? 0;
            $siswa->_d = $absensi['D'] ?? 0;
            return $siswa;
        });
    }

    public function headings(): array
    {
        return ['No', 'Nama Siswa', 'NISN', 'Kelas', 'Hadir', 'Sakit', 'Izin', 'Alpa', 'Dispensasi', 'Total'];
    }

    public function map($siswa): array
    {
        static $no = 0;
        $no++;
        $total = $siswa->_h + $siswa->_s + $siswa->_i + $siswa->_a + $siswa->_d;
        return [
            $no,
            $siswa->nama,
            $siswa->nisn ?? '-',
            $siswa->nama_rombel,
            $siswa->_h,
            $siswa->_s,
            $siswa->_i,
            $siswa->_a,
            $siswa->_d,
            $total,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '1e3a5f']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5, 'B' => 30, 'C' => 16, 'D' => 16,
            'E' => 10, 'F' => 10, 'G' => 10, 'H' => 10, 'I' => 14, 'J' => 10,
        ];
    }
}
