<?php
// app/Exports/PrestasiExport.php

namespace App\Exports;

use App\Models\KategoriPrestasi;
use App\Models\Prestasi;
use App\Models\PrestasiSiswa;
use App\Models\Rombel;
use App\Models\Semester;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PrestasiExport implements WithMultipleSheets
{
    public function __construct(private array $params) {}

    public function sheets(): array
    {
        return [
            new PrestasiDetailSheet($this->params),
            new PrestasiKategoriSheet($this->params),
            new PrestasiTingkatSheet($this->params),
            new PrestasiSiswaSheet($this->params),
        ];
    }
}

// ============================================================
// Sheet 1: Detail Semua Prestasi
// ============================================================
class PrestasiDetailSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(private array $params) {}

    public function title(): string { return 'Detail Prestasi'; }

    public function collection()
    {
        $p = $this->params;
        return Prestasi::with(['kategori','siswas','semester'])
            ->where('status','diverifikasi')
            ->when($p['semester_id'] ?? null, fn($q,$v) => $q->where('semester_id',$v))
            ->when($p['kategori_id'] ?? null, fn($q,$v) => $q->where('kategori_id',$v))
            ->when($p['tingkat']     ?? null, fn($q,$v) => $q->where('tingkat',$v))
            ->orderBy('juara_urut')->orderBy('tanggal','desc')
            ->get()
            ->map(fn($r, $i) => [
                'No'           => $i + 1,
                'Nama Lomba'   => $r->nama_lomba,
                'Penyelenggara'=> $r->penyelenggara ?? '-',
                'Kategori'     => $r->kategori?->nama ?? '-',
                'Tingkat'      => $r->tingkatLabel(),
                'Juara'        => $r->juara,
                'Tipe'         => ucfirst($r->tipe),
                'Nama Tim'     => $r->nama_tim ?? '-',
                'Siswa'        => $r->siswas->pluck('nama')->join(', '),
                'Kelas'        => $r->siswas->pluck('nama_rombel')->unique()->join(', '),
                'Tanggal'      => $r->tanggal->format('d/m/Y'),
                'Tempat'       => $r->tempat ?? '-',
                'Semester'     => $r->semester?->nama ?? '-',
            ]);
    }

    public function headings(): array
    {
        return ['No','Nama Lomba','Penyelenggara','Kategori','Tingkat','Juara','Tipe','Nama Tim','Siswa','Kelas','Tanggal','Tempat','Semester'];
    }

    public function columnWidths(): array
    {
        return ['A'=>5,'B'=>35,'C'=>25,'D'=>20,'E'=>15,'F'=>15,'G'=>12,'H'=>20,'I'=>35,'J'=>12,'K'=>12,'L'=>20,'M'=>20];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold'=>true,'color'=>['rgb'=>'FFFFFF']],
                  'fill' => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'1e3a5f']],
                  'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER]],
        ];
    }
}

// ============================================================
// Sheet 2: Rekap Per Kategori
// ============================================================
class PrestasiKategoriSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(private array $params) {}

    public function title(): string { return 'Rekap Per Kategori'; }

    public function collection()
    {
        $p = $this->params;
        $data = Prestasi::with('kategori')
            ->where('status','diverifikasi')
            ->when($p['semester_id'] ?? null, fn($q,$v) => $q->where('semester_id',$v))
            ->when($p['kategori_id'] ?? null, fn($q,$v) => $q->where('kategori_id',$v))
            ->when($p['tingkat']     ?? null, fn($q,$v) => $q->where('tingkat',$v))
            ->get()
            ->groupBy('kategori_id')
            ->map(fn($items) => [
                'Kategori'  => $items->first()->kategori?->nama ?? 'Tanpa Kategori',
                'Jenis'     => $items->first()->kategori?->jenisLabel() ?? '-',
                'Total'     => $items->count(),
                'Individu'  => $items->where('tipe','individu')->count(),
                'Tim'       => $items->where('tipe','tim')->count(),
                'Juara 1'   => $items->where('juara_urut',1)->count(),
                'Juara 2'   => $items->where('juara_urut',2)->count(),
                'Juara 3'   => $items->where('juara_urut',3)->count(),
            ])
            ->sortByDesc('Total')->values();

        return $data;
    }

    public function headings(): array
    {
        return ['Kategori','Jenis','Total','Individu','Tim','Juara 1','Juara 2','Juara 3'];
    }

    public function columnWidths(): array
    {
        return ['A'=>28,'B'=>16,'C'=>10,'D'=>12,'E'=>10,'F'=>10,'G'=>10,'H'=>10];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold'=>true,'color'=>['rgb'=>'FFFFFF']],
                  'fill' => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'16a34a']],
                  'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER]],
        ];
    }
}

// ============================================================
// Sheet 3: Rekap Per Tingkat
// ============================================================
class PrestasiTingkatSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(private array $params) {}

    public function title(): string { return 'Rekap Per Tingkat'; }

    public function collection()
    {
        $p = $this->params;
        $urut = ['internasional'=>1,'nasional'=>2,'provinsi'=>3,'kabupaten'=>4,'kecamatan'=>5,'sekolah'=>6];

        $data = Prestasi::where('status','diverifikasi')
            ->when($p['semester_id'] ?? null, fn($q,$v) => $q->where('semester_id',$v))
            ->when($p['kategori_id'] ?? null, fn($q,$v) => $q->where('kategori_id',$v))
            ->when($p['tingkat']     ?? null, fn($q,$v) => $q->where('tingkat',$v))
            ->get()
            ->groupBy('tingkat')
            ->map(fn($items, $t) => [
                'Tingkat'   => ucfirst($t),
                'Total'     => $items->count(),
                'Juara 1'   => $items->where('juara_urut',1)->count(),
                'Juara 2'   => $items->where('juara_urut',2)->count(),
                'Juara 3'   => $items->where('juara_urut',3)->count(),
                'Lainnya'   => $items->whereNotIn('juara_urut',[1,2,3])->count(),
                'Individu'  => $items->where('tipe','individu')->count(),
                'Tim'       => $items->where('tipe','tim')->count(),
            ])
            ->sortBy(fn($row) => $urut[strtolower($row['Tingkat'])] ?? 99)
            ->values();

        return $data;
    }

    public function headings(): array
    {
        return ['Tingkat','Total','Juara 1','Juara 2','Juara 3','Lainnya','Individu','Tim'];
    }

    public function columnWidths(): array
    {
        return ['A'=>18,'B'=>10,'C'=>10,'D'=>10,'E'=>10,'F'=>10,'G'=>12,'H'=>10];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold'=>true,'color'=>['rgb'=>'FFFFFF']],
                  'fill' => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'d97706']],
                  'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER]],
        ];
    }
}

// ============================================================
// Sheet 4: Rekap Per Siswa
// ============================================================
class PrestasiSiswaSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(private array $params) {}

    public function title(): string { return 'Rekap Per Siswa'; }

    public function collection()
    {
        $p = $this->params;
        return PrestasiSiswa::whereHas('prestasi', function ($q) use ($p) {
                $q->where('status','diverifikasi')
                  ->when($p['semester_id'] ?? null, fn($q,$v) => $q->where('semester_id',$v))
                  ->when($p['kategori_id'] ?? null, fn($q,$v) => $q->where('kategori_id',$v))
                  ->when($p['tingkat']     ?? null, fn($q,$v) => $q->where('tingkat',$v));
            })
            ->with(['siswa','prestasi'])
            ->get()
            ->groupBy('siswa_id')
            ->map(fn($items, $i) => [
                'No'           => '',
                'Nama Siswa'   => $items->first()->siswa?->nama ?? '-',
                'NISN'         => $items->first()->siswa?->nisn ?? '-',
                'Kelas'        => $items->first()->siswa?->nama_rombel ?? '-',
                'Total'        => $items->count(),
                'Nasional/Int' => $items->filter(fn($i) => in_array($i->prestasi?->tingkat,['nasional','internasional']))->count(),
                'Provinsi'     => $items->filter(fn($i) => $i->prestasi?->tingkat === 'provinsi')->count(),
                'Kab/Kota'     => $items->filter(fn($i) => $i->prestasi?->tingkat === 'kabupaten')->count(),
            ])
            ->sortByDesc('Total')
            ->values()
            ->map(function ($row, $i) {
                $row['No'] = $i + 1;
                return $row;
            });
    }

    public function headings(): array
    {
        return ['No','Nama Siswa','NISN','Kelas','Total Prestasi','Nasional/Int','Provinsi','Kab/Kota'];
    }

    public function columnWidths(): array
    {
        return ['A'=>5,'B'=>35,'C'=>15,'D'=>12,'E'=>15,'F'=>14,'G'=>12,'H'=>12];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold'=>true,'color'=>['rgb'=>'FFFFFF']],
                  'fill' => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'7c3aed']],
                  'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER]],
        ];
    }
}
