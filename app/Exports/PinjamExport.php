<?php

namespace App\Exports;

use App\Models\Pinjam;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PinjamExport implements FromCollection, WithMapping, WithHeadings
{
    public function __construct(
        private string $startDate,
        private string $endDate,
    ) {}

    public function collection()
    {
        return Pinjam::with([
            'pinjam_detail' => fn($query) => $query->where('status', 'Pinjam'),
            'pinjam_detail.buku',
            'pinjam_detail.petugas_kembali',
            'petugas_pinjam',
        ])
            ->whereHas('pinjam_detail', fn($query) => $query->where('status', 'Pinjam'))
            ->whereBetween('tgl_pinjam', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ])
            ->orderByDesc('no_pinjam')
            ->get();
    }

    public function map($row): array
    {
        $mappedRows = [];

        foreach ($row->pinjam_detail as $detail) {
            $mappedRows[] = [
                $row->no_pinjam,
                Carbon::parse($row->tgl_pinjam)->format('d-m-Y'),
                $detail->buku->judul_buku ?? 'Tidak Ada Judul',
                $detail->status,
                $row->petugas_pinjam->nama ?? 'Tidak Ada Petugas',
            ];
        }

        return $mappedRows;
    }

    public function headings(): array
    {
        return [
            'No Peminjaman',
            'Tanggal Pinjam',
            'Judul Buku',
            'Status',
            'Petugas Pinjam',
        ];
    }
}
