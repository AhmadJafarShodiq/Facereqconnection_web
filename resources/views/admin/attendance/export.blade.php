<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Export Absensi</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th,
        td {
            padding: 5px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h3>Data Absensi</h3>
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
            @foreach($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->tanggal->format('Y-m-d') }}</td>
                    <td>{{ $attendance->user->username }}</td>
                    <td>{{ $attendance->user->profile->nama_lengkap ?? '-' }}</td>
                    <td>{{ optional($attendance->jam_masuk)->format('H:i:s') ?? '-' }}</td>
                    <td>{{ optional($attendance->jam_pulang)->format('H:i:s') ?? '-' }}</td>
                    <td>{{ ucfirst($attendance->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
