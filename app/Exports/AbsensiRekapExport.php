<?php
// app/Exports/AbsensiRekapExport.php

namespace App\Exports;

use App\Models\AbsensiHarian;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class AbsensiRekapExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Rekap Absensi Kelas';
    }

    public function collection()
    {
        return AbsensiHarian::selectRaw('
                rombongan_belajar_id,
                nama_rombel,
                MONTH(tanggal) as bulan,
                YEAR(tanggal) as tahun,
                COUNT(*) as total_hari,
                SUM((SELECT COUNT(*) FROM absensi_siswa WHERE absensi_harian_id = absensi_harian.id AND status = "H")) as hadir,
                SUM((SELECT COUNT(*) FROM absensi_siswa WHERE absensi_harian_id = absensi_harian.id AND status = "S")) as sakit,
                SUM((SELECT COUNT(*) FROM absensi_siswa WHERE absensi_harian_id = absensi_harian.id AND status = "I")) as izin,
                SUM((SELECT COUNT(*) FROM absensi_siswa WHERE absensi_harian_id = absensi_harian.id AND status = "A")) as alpa,
                SUM((SELECT COUNT(*) FROM absensi_siswa WHERE absensi_harian_id = absensi_harian.id AND status = "D")) as dispensasi
            ')
            ->when($this->filters['semester_id'] ?? null, fn($q, $v) => $q->where('semester_id', $v))
            ->when($this->filters['rombel_id'] ?? null, fn($q, $v) => $q->where('rombongan_belajar_id', $v))
            ->when($this->filters['bulan'] ?? null, fn($q, $v) => $q->whereMonth('tanggal', $v))
            ->groupBy('rombongan_belajar_id', 'nama_rombel', DB::raw('MONTH(tanggal)'), DB::raw('YEAR(tanggal)'))
            ->orderBy('nama_rombel')
            ->orderBy(DB::raw('MONTH(tanggal)'))
            ->get();
    }

    public function headings(): array
    {
        return ['No', 'Kelas', 'Bulan', 'Tahun', 'Total Hari', 'Hadir', 'Sakit', 'Izin', 'Alpa', 'Dispensasi'];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->nama_rombel,
            Carbon::create()->month($row->bulan)->translatedFormat('F'),
            $row->tahun,
            $row->total_hari,
            $row->hadir,
            $row->sakit,
            $row->izin,
            $row->alpa,
            $row->dispensasi,
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
            'A' => 5, 'B' => 20, 'C' => 14, 'D' => 8,
            'E' => 12, 'F' => 10, 'G' => 10, 'H' => 10, 'I' => 10, 'J' => 14,
        ];
    }
}
