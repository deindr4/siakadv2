<?php
// app/Exports/TiketExport.php

namespace App\Exports;

use App\Models\Tiket;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TiketExport implements WithMultipleSheets
{
    public function __construct(private array $params) {}

    public function sheets(): array
    {
        return [
            new TiketRekapKategoriSheet($this->params),
            new TiketRekapBulanSheet($this->params),
            new TiketDetailSheet($this->params),
        ];
    }
}

// ============================================================
// Sheet 1: Rekap Per Kategori
// ============================================================
class TiketRekapKategoriSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(private array $p) {}
    public function title(): string { return 'Rekap Per Kategori'; }

    public function collection()
    {
        return Tiket::selectRaw('kategori, kategori_lainnya, COUNT(*) as total,
                SUM(CASE WHEN status="open" THEN 1 ELSE 0 END) as open,
                SUM(CASE WHEN status="diproses" THEN 1 ELSE 0 END) as diproses,
                SUM(CASE WHEN status="selesai" THEN 1 ELSE 0 END) as selesai,
                SUM(CASE WHEN status="terkunci" THEN 1 ELSE 0 END) as terkunci,
                SUM(CASE WHEN role_pembuat="siswa" THEN 1 ELSE 0 END) as dari_siswa,
                SUM(CASE WHEN role_pembuat="guru" THEN 1 ELSE 0 END) as dari_guru')
            ->whereYear('created_at', $this->p['tahun'] ?? now()->year)
            ->when($this->p['bulan']    ?? null, fn($q,$v) => $q->whereMonth('created_at', $v))
            ->when($this->p['kategori'] ?? null, fn($q,$v) => $q->where('kategori', $v))
            ->groupBy('kategori', 'kategori_lainnya')
            ->orderByDesc('total')
            ->get()
            ->map(fn($r, $i) => [
                'No'          => $i + 1,
                'Kategori'    => $r->kategori === 'lainnya' && $r->kategori_lainnya
                                    ? $r->kategori_lainnya
                                    : (Tiket::kategoriList()[$r->kategori] ?? ucfirst($r->kategori)),
                'Total'       => $r->total,
                'Terbuka'     => $r->open,
                'Diproses'    => $r->diproses,
                'Selesai'     => $r->selesai,
                'Terkunci'    => $r->terkunci,
                'Dari Siswa'  => $r->dari_siswa,
                'Dari Guru'   => $r->dari_guru,
            ]);
    }

    public function headings(): array
    {
        return ['No','Kategori','Total','Terbuka','Diproses','Selesai','Terkunci','Dari Siswa','Dari Guru'];
    }

    public function columnWidths(): array
    {
        return ['A'=>5,'B'=>32,'C'=>10,'D'=>12,'E'=>12,'F'=>12,'G'=>12,'H'=>14,'I'=>12];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold'=>true,'color'=>['rgb'=>'FFFFFF']],
                  'fill' => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'6366f1']],
                  'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER]],
        ];
    }
}

// ============================================================
// Sheet 2: Rekap Per Bulan
// ============================================================
class TiketRekapBulanSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(private array $p) {}
    public function title(): string { return 'Rekap Per Bulan'; }

    public function collection()
    {
        return Tiket::selectRaw('MONTH(created_at) as bulan_num, MONTHNAME(created_at) as bulan_nama,
                COUNT(*) as total,
                SUM(CASE WHEN status="selesai" THEN 1 ELSE 0 END) as selesai,
                SUM(CASE WHEN status IN ("open","diproses") THEN 1 ELSE 0 END) as aktif,
                SUM(CASE WHEN role_pembuat="siswa" THEN 1 ELSE 0 END) as dari_siswa,
                SUM(CASE WHEN role_pembuat="guru" THEN 1 ELSE 0 END) as dari_guru')
            ->whereYear('created_at', $this->p['tahun'] ?? now()->year)
            ->when($this->p['kategori'] ?? null, fn($q,$v) => $q->where('kategori', $v))
            ->groupBy('bulan_num', 'bulan_nama')
            ->orderBy('bulan_num')
            ->get()
            ->map(fn($r) => [
                'Bulan'      => $r->bulan_nama,
                'Total'      => $r->total,
                'Aktif'      => $r->aktif,
                'Selesai'    => $r->selesai,
                'Dari Siswa' => $r->dari_siswa,
                'Dari Guru'  => $r->dari_guru,
            ]);
    }

    public function headings(): array
    {
        return ['Bulan','Total','Aktif','Selesai','Dari Siswa','Dari Guru'];
    }

    public function columnWidths(): array
    {
        return ['A'=>16,'B'=>10,'C'=>10,'D'=>12,'E'=>14,'F'=>12];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold'=>true,'color'=>['rgb'=>'FFFFFF']],
                  'fill' => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'0284c7']],
                  'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER]],
        ];
    }
}

// ============================================================
// Sheet 3: Detail Semua Tiket
// ============================================================
class TiketDetailSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(private array $p) {}
    public function title(): string { return 'Detail Tiket'; }

    public function collection()
    {
        return Tiket::with(['user','respon'])
            ->whereYear('created_at', $this->p['tahun'] ?? now()->year)
            ->when($this->p['bulan']    ?? null, fn($q,$v) => $q->whereMonth('created_at', $v))
            ->when($this->p['kategori'] ?? null, fn($q,$v) => $q->where('kategori', $v))
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($t, $i) => [
                'No'          => $i + 1,
                'Judul'       => $t->judul,
                'Kategori'    => $t->kategoriLabel(),
                'Dari'        => $t->is_anonim ? 'Anonim (' . ucfirst($t->role_pembuat) . ')' : ($t->user?->name ?? '-'),
                'Role'        => ucfirst($t->role_pembuat),
                'Prioritas'   => ucfirst($t->prioritas),
                'Status'      => $t->statusLabel(),
                'Jml Respon'  => $t->respon->count(),
                'Tanggal'     => $t->created_at->format('d/m/Y H:i'),
                'Terakhir Aktif' => ($t->last_response_at ?? $t->created_at)->format('d/m/Y H:i'),
            ]);
    }

    public function headings(): array
    {
        return ['No','Judul','Kategori','Dari','Role','Prioritas','Status','Jml Respon','Tanggal','Terakhir Aktif'];
    }

    public function columnWidths(): array
    {
        return ['A'=>5,'B'=>35,'C'=>25,'D'=>28,'E'=>12,'F'=>12,'G'=>14,'H'=>13,'I'=>18,'J'=>18];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold'=>true,'color'=>['rgb'=>'FFFFFF']],
                  'fill' => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'374151']],
                  'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER]],
        ];
    }
}
