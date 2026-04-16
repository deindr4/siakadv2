<?php

namespace App\Exports;

use App\Models\PelanggaranSiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PelanggaranExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithTitle
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return $this->query->get();
    }

    public function title(): string
    {
        return 'Rekap Pelanggaran';
    }

    public function headings(): array
    {
        return [
            'No', 'Tanggal', 'Nama Siswa', 'NISN', 'Kelas',
            'Jenis Pelanggaran', 'Kategori', 'Poin',
            'Keterangan', 'Tindakan', 'Dicatat Oleh', 'Status',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->tanggal?->format('d/m/Y'),
            $row->siswa?->nama,
            $row->siswa?->nisn,
            $row->siswa?->nama_rombel,
            $row->jenisPelanggaran?->nama,
            ucfirst($row->jenisPelanggaran?->kategori),
            $row->poin,
            $row->keterangan,
            $row->tindakan,
            $row->dicatatOleh?->name,
            ucfirst($row->status),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => 'DC2626']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,  'B' => 12, 'C' => 25, 'D' => 15,
            'E' => 15, 'F' => 30, 'G' => 12, 'H' => 8,
            'I' => 30, 'J' => 25, 'K' => 20, 'L' => 12,
        ];
    }
}
