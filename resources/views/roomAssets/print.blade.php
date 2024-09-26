<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Asset- Cetak</title>
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
            <h1>PMI DIKLAT Room Asset - Cetak</h1>
            <p><strong>Kota Semarang</strong></p>
            <p>Jl. Pandanaran No. XX, Semarang, Telp: (024) XXXXXXX</p>
        </div>
    </div>

    <!-- Tabel Data -->
    <table class="table table-hover table-bordered" id="table3">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Ruangan</th>
                <th>Nama Item</th>
                <th>Kondisi</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $currentRoom = null;
                $rowspan = 0;
            @endphp
            @foreach($records as $key => $record)
                @if ($record->room?->room_name !== $currentRoom)
                    @php
                        $rowspan = $records->where('room.room_name', $record->room?->room_name)->count();
                        $currentRoom = $record->room?->room_name;
                    @endphp
                    <tr>
                        <td rowspan="{{ $rowspan }}">{{ $loop->iteration }}</td>
                        <td rowspan="{{ $rowspan }}">{{ $record->room?->room_name }}</td>
                        <td>{{ $record->inventory?->name }}</td>
                        <td>{{ $record->isBroken ? 'Rusak' : 'Normal' }}</td>
                        <td>{{ $record->comment }}</td>
                    </tr>
                @else
                    <tr>
                        <td>{{ $record->inventory?->name }}</td>
                        <td>{{ $record->isBroken ? 'Rusak' : 'Normal' }}</td>
                        <td>{{ $record->comment }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>

        <tfoot>
        </tfoot>
    </table>
    <h10>Perhatian :</h1>
    <p>Semua Perlengkapan/Item Tercatat per-hari dimana Laporan Dibuat</p>
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