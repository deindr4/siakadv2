<?php

namespace App\Exports;

use App\Models\JurnalMengajar;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JurnalExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithTitle
{
    protected $query;
    protected $title;

    public function __construct($query, string $title = 'Jurnal Mengajar')
    {
        $this->query = $query;
        $this->title = $title;
    }

    public function collection()
    {
        return $this->query->get();
    }

    public function title(): string
    {
        return $this->title;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Hari',
            'Guru',
            'NIP',
            'Mata Pelajaran',
            'Kelas',
            'Pertemuan Ke',
            'Jam Ke',
            'Jam Mulai',
            'Jam Selesai',
            'Materi / KD',
            'Kegiatan Pembelajaran',
            'Catatan',
            'Jumlah Hadir',
            'Tidak Hadir',
            'Status',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->tanggal?->format('d/m/Y'),
            $row->tanggal?->translatedFormat('l'),
            $row->guru?->nama,
            $row->guru?->nip,
            $row->mataPelajaran?->nama,
            $row->nama_rombel,
            $row->pertemuan_ke,
            $row->jam_ke,
            $row->jam_mulai?->format('H:i'),
            $row->jam_selesai?->format('H:i'),
            $row->materi,
            $row->kegiatan,
            $row->catatan,
            $row->jumlah_hadir,
            $row->jumlah_tidak_hadir,
            ucfirst($row->status),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F46E5']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 12,
            'C' => 12,
            'D' => 25,
            'E' => 20,
            'F' => 20,
            'G' => 15,
            'H' => 12,
            'I' => 8,
            'J' => 10,
            'K' => 10,
            'L' => 35,
            'M' => 40,
            'N' => 25,
            'O' => 12,
            'P' => 12,
            'Q' => 12,
        ];
    }
}
