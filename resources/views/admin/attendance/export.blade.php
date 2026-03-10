<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi</title>
    <style>
        @page { margin: 20px; }
        body { font-family: sans-serif; font-size: 9px; margin: 0; padding: 0; }
        h2, h3, h4 { margin: 5px 0; text-align: center; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #000;
            padding: 2px;
            text-align: center;
            overflow: hidden;
            white-space: nowrap;
        }
        th { background: #eee; }
        .nama-col { width: 120px; text-align: left; padding-left: 5px; }
        .day-col { width: 22px; }
        .rekap-col { width: 35px; background: #f9f9f9; }
        .legend { margin-top: 10px; font-size: 8px; }
    </style>
</head>
<body>

<h2>REKAP ABSENSI</h2>
<p style="text-align:center; margin-bottom: 20px;">
    Bulan: {{ $bulan ?? '-' }} | Tahun: {{ $tahun ?? '-' }}
</p>

{{-- ================= GURU ================= --}}
@if($rekapGuru->count() > 0)
<h3>Rekap Guru</h3>
<table>
    <thead>
        <tr>
            <th class="nama-col">Nama Guru</th>
            @for($i = 1; $i <= $daysInMonth; $i++)
                <th class="day-col">{{ $i }}</th>
            @endfor
            <th class="rekap-col">H</th>
            <th class="rekap-col">T</th>
            <th class="rekap-col">Tot</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rekapGuru as $g)
        <tr>
            <td class="nama-col">{{ $g['nama'] }}</td>
            @for($i = 1; $i <= $daysInMonth; $i++)
                <td>{{ $g['days'][$i] }}</td>
            @endfor
            <td class="rekap-col">{{ $g['hadir'] }}</td>
            <td class="rekap-col">{{ $g['terlambat'] }}</td>
            <td class="rekap-col">{{ $g['total'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ================= SISWA ================= --}}
@if($rekapSiswa->count() > 0)
    @foreach($rekapSiswa as $kelas)
    <h3>Rekap Siswa - Kelas: {{ $kelas['nama_kelas'] }}</h3>
    <table>
        <thead>
            <tr>
                <th class="nama-col">Nama Siswa</th>
                @for($i = 1; $i <= $daysInMonth; $i++)
                    <th class="day-col">{{ $i }}</th>
                @endfor
                <th class="rekap-col">H</th>
                <th class="rekap-col">T</th>
                <th class="rekap-col">Tot</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kelas['siswa'] as $s)
            <tr>
                <td class="nama-col">{{ $s['nama'] }}</td>
                @for($i = 1; $i <= $daysInMonth; $i++)
                    <td>{{ $s['days'][$i] }}</td>
                @endfor
                <td class="rekap-col">{{ $s['hadir'] }}</td>
                <td class="rekap-col">{{ $s['terlambat'] }}</td>
                <td class="rekap-col">{{ $s['total'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endforeach
@endif

<div class="legend">
    <strong>Keterangan:</strong><br>
    H: Hadir | T: Terlambat | P: Pulang (Guru) | PD: Pulang Dini (Guru) | -: Tanpa Keterangan / Libur
</div>

<div style="margin-top: 30px;">
    <table style="border: none !important; width: 100%;">
        <tr style="border: none !important;">
            <td style="border: none !important; text-align: left; width: 70%;"></td>
            <td style="border: none !important; text-align: right;">
                Dicetak pada: {{ now()->format('d/m/Y H:i') }}<br><br><br><br>
                ( _________________________ )<br>
                Kepala Sekolah
            </td>
        </tr>
    </table>
</div>

</body>
</html>