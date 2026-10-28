<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Data Peminjaman</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        h2 {
            margin-bottom: 4px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 5px;
            text-align: left;
        }

        th {
            background: #eee;
        }
    </style>
</head>

<body>
    <h2>Data Peminjaman Aktif</h2>
    <p>Periode: {{ $startDate }} sampai {{ $endDate }}</p>
    <table>
        <thead>
            <tr>
                <th>No. Pinjam</th>
                <th>Tanggal Pinjam</th>
                <th>Batas Kembali</th>
                <th>Judul Buku</th>
                <th>Status</th>
                <th>Anggota</th>
                <th>Petugas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $pinjam)
            @foreach ($pinjam->pinjam_detail as $detail)
            <tr>
                <td>{{ $pinjam->no_pinjam }}</td>
                <td>{{ \Carbon\Carbon::parse($pinjam->tgl_pinjam)->format('d-m-Y') }}</td>
                <td>{{ $detail->tgl_kembali ? \Carbon\Carbon::parse($detail->tgl_kembali)->format('d-m-Y') : '' }}</td>
                <td>{{ $detail->buku->judul_buku ?? '' }}</td>
                <td>{{ $detail->status }}</td>
                <td>{{ $pinjam->anggota->nama ?? 'Tidak Ada Anggota' }}</td>
                <td>{{ $pinjam->petugas_pinjam->nama ?? 'Tidak Ada Petugas' }}</td>
            </tr>
            @endforeach
            @endforeach
        </tbody>
    </table>
</body>

</html>