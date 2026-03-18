<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Raffle Winners - {{ $prizeName }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
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
            /* Tailwind Blue-600 */
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
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
            font-family: monospace;
            font-weight: bold;
            width: 25%;
        }

        /* Ensure table doesn't cut rows awkwardly across pages */
        tr {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>Official Cedea Eomuk Prize Results</h1>
        <p>Prize: <span class="prize-name">{{ $prizeName }}</span></p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Raffle Number</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($winners as $index => $winner)
                <tr>
                    <td class="no-col">{{ $index + 1 }}</td>
                    <td class="raffle-col">{{ $winner['raffle_number'] }}</td>
                    <td>{{ $winner['name'] }}</td>
                    <td>{{ $winner['email'] }}</td>
                    <td>{{ $winner['phone'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
