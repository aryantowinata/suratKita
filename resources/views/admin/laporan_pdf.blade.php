<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Surat</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th,
    td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    h2,
    h4 {
        text-align: center;
        margin-bottom: 10px;
    }
    </style>
</head>

<body>

    <h2>Laporan Surat</h2>
    <h4>Periode:
        @if (!$startDate && !$endDate)
        Semua Waktu
        @else
        {{ $startDate ? $startDate : 'Semua Waktu' }} - {{ $endDate ? $endDate : 'Semua Waktu' }}
        @endif
    </h4>


    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Jenis Surat</th>
                <th>Nomor Surat</th>
                <th>Pengirim/Penerima</th>
                <th>Perihal</th>
                <th>Tanggal Surat</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suratMasuk as $index => $surat)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>Surat Masuk</td>
                <td>{{ $surat->nomor_surat }}</td>
                <td>{{ $surat->pengirim }}</td>
                <td>{{ $surat->perihal }}</td>
                <td>{{ $surat->tanggal_surat }}</td>
                <td>{{ $surat->status }}</td>
            </tr>
            @endforeach
            @foreach($suratKeluar as $index => $surat)
            <tr>
                <td>{{ $index + count($suratMasuk) + 1 }}</td>
                <td>Surat Keluar</td>
                <td>{{ $surat->nomor_surat }}</td>
                <td>{{ $surat->pengirim }}</td>
                <td>{{ $surat->perihal }}</td>
                <td>{{ $surat->tanggal_surat }}</td>
                <td>{{ $surat->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>