<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Bukti Booking</title>
    <style>
        body {
            color: #333;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        h1 {
            margin-bottom: 8px;
            text-align: center;
        }

        .notice {
            margin-bottom: 18px;
            text-align: center;
        }

        .booking-info,
        .booking-items {
            border-collapse: collapse;
            width: 100%;
        }

        .booking-info {
            margin: 0 auto 18px;
            width: 70%;
        }

        .booking-info th,
        .booking-info td,
        .booking-items th,
        .booking-items td {
            border: 1px solid #b8c2cc;
            padding: 7px;
            text-align: left;
        }

        .booking-info th,
        .booking-items th {
            background: #e8eef3;
        }

        .booking-items {
            margin-bottom: 22px;
        }

        .booking-items thead {
            display: table-header-group;
        }
    </style>
</head>

<body>
    <h1>Bukti Booking Buku</h1>
    <p class="notice">Buku harus diambil maksimal 1 x 24 jam setelah booking.</p>

    @foreach ($data_booking as $booking)
    <table class="booking-info">
        <tbody>
            <tr>
                <th>ID Booking</th>
                <td>{{ $booking->id_booking }}</td>
                <th>Tanggal Booking</th>
                <td>{{ $booking->tgl_booking }}</td>
                <th>Batas Ambil</th>
                <td>{{ $booking->batas_ambil }}</td>
            </tr>
        </tbody>
    </table>

    <table class="booking-items">
        <thead>
            <tr>
                <th>#</th>
                <th>ID Booking</th>
                <th>Judul Buku</th>
                <th>Pengarang</th>
                <th>Penerbit</th>
                <th>Tahun Terbit</th>
                <th>Kategori</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($booking->booking_detail as $detail)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $booking->id_booking }}</td>
                <td>{{ $detail->buku->judul_buku ?? 'Buku tidak tersedia' }}</td>
                <td>{{ $detail->buku->pengarang ?? '-' }}</td>
                <td>{{ $detail->buku->penerbit ?? '-' }}</td>
                <td>{{ $detail->buku->tahun_terbit ?? '-' }}</td>
                <td>{{ $detail->buku->kategori->nama_kategori ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endforeach
</body>

</html>