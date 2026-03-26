<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Raffle Winners - {{ $prizeName }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .header p {
            margin: 0;
            font-size: 14px;
            color: #666;
        }

        .prize-name {
            color: #2563eb;
            font-weight: bold;
        }

        table {
            width: 100%;
            font-family: Arial, sans-serif;
            border-collapse: collapse;
            font-size: 13px;
        }

        table,
        td,
        th {
            font-family: Arial, sans-serif;
        }

        th,
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f8fafc;
            color: #475569;
            text-transform: uppercase;
            font-weight: bold;
        }

        .no-col {
            color: #2563eb;
            font-weight: bold;
            width: 5%;
        }

        .raffle-col {
            font-weight: bold;
            width: 25%;
        }

        /* Ensure table doesn't cut rows awkwardly across pages */
        tr {
            page-break-inside: avoid;
        }

        .signature {
            border-collapse: collapse;
        }
    </style>
</head>

<body style="position: relative;">

    <div class="header">
        <h1>Daftar Pemenang “EOMUK CHILL IN SEOUL”</h1>
        <p>PT Citradimensi Arthali</p>
        <p>Jakarta, {{ now()->setTimezone('Asia/Jakarta')->translatedFormat('F j, Y') }}</p>
    </div>

    <table style="margin-bottom: 100px;">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Kode unik</th>
                <th>No. Handphone</th>
                <th>Jenis Hadiah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($winners as $index => $winner)
                <tr>
                    <td class="no-col">{{ $index + 1 }}</td>
                    <td>{{ $winner['name'] }}</td>
                    <td class="raffle-col">{{ $winner['raffle_number'] }}</td>
                    <td>{{ $winner['phone'] }}</td>
                    <td>{{ $prizeName }}</td>
                </tr>
            @endforeach
            <div style="display:inline-block; position: absolute; bottom:30px; ">
                <table class="signature">
                    <tbody>
                        @php
                            $signatures = [
                                'Kementerian sosial RI',
                                'Kementerian sosial RI',
                                'dinas sosial provinsi dki jakarta',
                                'dinas sosial provinsi dki jakarta',
                                'notaris',
                                'agency',
                                'penyelenggara',
                                'penyelenggara',
                            ];

                            $chunkedSignatures = array_chunk($signatures, 4);
                        @endphp

                        @foreach ($chunkedSignatures as $row)
                            <tr>
                                @foreach ($row as $signature)
                                    <td style="border: 1px solid black; padding: 0; text-align: center;">
                                        <div
                                            style="border-bottom: 1px solid black; padding: 10px 20px; font-weight: 900; text-transform: uppercase; height: 50px; text-align: center; vertical-align:middle;">
                                            {{ $signature }}
                                        </div>
                                        <div style="height: 100px;">
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </tbody>
    </table>



</body>

</html>
