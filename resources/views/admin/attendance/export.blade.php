<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px }
        h2, h3 { margin: 5px 0 }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
        th { background: #eee }
    </style>
</head>
<body>

<h2 style="text-align:center;">REKAP ABSENSI</h2>
<p style="text-align:center;">
    Bulan: {{ $bulan ?? '-' }} |
    Tahun: {{ $tahun ?? '-' }}
</p>

{{-- ================= GURU ================= --}}
<h3>Rekap Guru</h3>

<table>
<tr>
    <th>Nama</th>
<th>Hadir</th>
<th>Terlambat</th>
<th>Total</th>
</tr>
@foreach($rekapGuru as $g)
<tr>
    <td>{{ $g['nama'] }}</td>
    <td>{{ $g['hadir'] }}</td>
    <td>{{ $g['terlambat'] ?? 0 }}</td>
    <td>{{ $g['total'] }}</td>
</tr>
@endforeach
</table>

{{-- ================= SISWA ================= --}}
<h3>Rekap Siswa</h3>

@foreach($rekapSiswa as $kelas)
<h4>Kelas: {{ $kelas['nama_kelas'] }}</h4>

<table>
<tr>
  <th>Nama</th>
<th>Hadir</th>
<th>Terlambat</th>
<th>Total</th>
</tr>
@foreach($kelas['siswa'] as $s)
<tr>
    <td>{{ $s['nama'] }}</td>
<td>{{ $s['hadir'] }}</td>
<td>{{ $s['terlambat'] }}</td>
<td>{{ $s['total'] }}</td>
</tr>
@endforeach
</table>
@endforeach

<br><br>
<p style="text-align:right;">
Mengetahui,<br>
Kepala Sekolah<br><br><br>
_________________________
</p>

</body>
</html>