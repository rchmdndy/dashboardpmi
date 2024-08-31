<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kos - Cetak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Tambahkan CSS jika diperlukan -->
    <style>
        /* Aturan CSS */
        .fade-row {
            opacity: 0.5;
            /* Sesuaikan dengan tingkat opasitas yang diinginkan */
            transition: opacity 0.3s ease;
            /* Animasi perubahan opasitas */
        }

        .fade-row:hover {
            opacity: 1;
            /* Opasitas kembali ke normal saat dihover */
        }

        .fade-row td {
            text-decoration: line-through;
        }

        th, td {
            text-align: center;
            vertical-align: middle; /* Posisi teks secara vertikal di tengah */
        }

        /* Styling untuk kop surat */
        .letterhead {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center; /* Memusatkan secara horizontal */
            padding: 20px;
            position: relative; /* Menjadikan posisi relatif untuk menempatkan garis */
            text-align: center; /* Memastikan teks terpusat */
        }

        .letterhead h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .letterhead img {
            width: 170px; /* Ukuran default */
            height: auto; /* Lebar akan menyesuaikan proporsi */
            margin-right: 70px; /* Margin untuk memberi jarak antara logo dan teks */
        }

        /* Styling untuk tabel */
        .table-bordered th, .table-bordered td {
            border: 1px solid #dee2e6; /* Warna garis */
            padding: .75rem; /* Padding sel */
        }

        /* Pewarnaan tiap baris tabel */
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f8f9fa; /* Warna latar belakang bergantian */
        }

        /* Efek shadow pada tiap baris tabel */
        .table tbody tr:hover {
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1); /* Shadow ketika dihover */
        }

        .signature {
            position: relative;
            bottom: 0;
            right: 0;
            margin-bottom: 30px; /* Jarak antara tanda tangan dan nama bertanda tangan */
            margin-right: 50px;
            text-align: right; /* Memastikan teks terletak di kanan */
        }
        /* Penyesuaian jarak antara tanggal dan nama yang bertanda tangan */
        .signature p {
            margin-bottom: 90px; /* Jarak antara elemen dalam div signature */
        }

    </style>
</head>

<body onload="window.print()">
    <!-- Kop Surat -->
    <div class="letterhead">
    <img src="{{ asset('images/logo_asli.png') }}" alt="Logo" style="width: 120px; height: auto;">
        <div>
            <h1>PMI DIKLAT Report - Cetak</h1>
            <p><strong>Kota Semarang</strong></p>
            <p>Jl. Pandanaran No. XX, Semarang, Telp: (024) XXXXXXX</p>
        </div>
    </div>

    <!-- Tabel Data -->
    <table class="table table-hover table-bordered table-striped" id="table3">
        <thead>
            <tr>
                <th>No</th>
                <th>Tipe Ruangan</th>
                <th>Jumlah Pemesanan</th>
                <th>Total Pendapatan</th>
                <th>Bulan, Tahun</th>
            </tr>
        </thead>
        <tbody>
        @foreach($records as $record)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $record->roomType->room_type }}</td>
                <td>{{ $record->total_bookings }}</td>
                <td>{{ 'Rp. ' . number_format($record->total_income, 0, ',', '.') }}</td>
                <td>{{ $record->created_at->translatedFormat('F, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
    <tr>
        <td colspan="3"><strong>Total Pendapatan Keseluruhan:</strong></td>
        <td><strong>{{ 'Rp. ' . number_format($totalIncome, 0, ',', '.') }}</strong></td>
    </tr>
</tfoot>

    </table>

    <div class="signature">
        <p id="tanggal">{{ date("Y-m-d") }}</p>
        <p id="nama"><span id="nama_pengguna">{{ $user->name }}</span></p>
    </div>

    <script>
        window.onload = fillSignature;

        function fillSignature() {
            window.history.replaceState({}, document.title, window.location.pathname, window.location);
            window.print();
        }
    </script>
</body>

</html>