<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi</title>
    <style>
        body { font-size: 12px }
        h2, h3, h4 { margin-bottom: 5px }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
        }
        th { background: #eee }
    </style>
</head>
<body>

<h2>Rekap Absensi</h2>

{{-- ================= GURU ================= --}}
<h3>ROLE: GURU</h3>

@foreach($attendances['guru'] as $bulan => $items)
    <strong>Bulan: {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}</strong>

    <table>
        <thead>
        <tr>
            <th>Tanggal</th>
            <th>Username</th>
            <th>Nama</th>
            <th>Jam Masuk</th>
            <th>Jam Pulang</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $a)
            <tr>
                <td>{{ $a->tanggal->format('d-m-Y') }}</td>
                <td>{{ $a->user->username }}</td>
                <td>{{ $a->user->profile->nama_lengkap ?? '-' }}</td>
                <td>{{ optional($a->jam_masuk)->format('H:i') ?? '-' }}</td>
                <td>{{ optional($a->jam_pulang)->format('H:i') ?? '-' }}</td>
                <td>{{ ucfirst($a->status) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endforeach


{{-- ================= SISWA ================= --}}
<h3>ROLE: SISWA</h3>

@foreach($attendances['siswa'] as $kelasId => $perBulan)
    <h4>Kelas: {{ optional($perBulan->first()->first()->kelas)->nama ?? '-' }}</h4>

    @foreach($perBulan as $bulan => $items)
        <strong>Bulan: {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}</strong>

        <table>
            <thead>
            <tr>
                <th>Tanggal</th>
                <th>Username</th>
                <th>Nama</th>
                <th>Jam Masuk</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $a)
                <tr>
                    <td>{{ $a->tanggal->format('d-m-Y') }}</td>
                    <td>{{ $a->user->username }}</td>
                    <td>{{ $a->user->profile->nama_lengkap ?? '-' }}</td>
                    <td>{{ optional($a->jam_masuk)->format('H:i') ?? '-' }}</td>
                    <td>{{ ucfirst($a->status) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endforeach
@endforeach

</body>
</html>
