<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raffle Winners - {{ $prizeName ?? 'all' }}</title>

    @vite(['resources/css/app.css'])
</head>

<body
    class="relative mx-auto flex max-w-7xl flex-col justify-between bg-white px-12 py-4 font-sans text-gray-800 antialiased">

    <div class="border-gray-100 pb-6 text-center">
        <h1 class="text-lg font-bold uppercase tracking-widest text-gray-900">
            Daftar Pemenang "EOMUK CHILL IN SEOUL"
        </h1>

        <p class="text-base font-semibold tracking-wide text-gray-600">
            PT Citradimensi Arthali
        </p>
        <p class="text-xs text-gray-500">
            Jakarta, {{ now()->setTimezone('Asia/Jakarta')->translatedFormat('F j, Y') }}
        </p>
    </div>

    @php
        $chunkedWinners = collect($winners)->chunk(20);
    @endphp
    @foreach ($chunkedWinners as $chunk)
        <div class="my-6 mb-auto overflow-hidden rounded-lg">
            <table class="min-w-full table-auto border-collapse text-xs">
                <thead class="border-b border-gray-200 bg-slate-50">
                    <tr>
                        <th class="w-16 py-2 text-left font-bold uppercase tracking-wider text-slate-600"
                            scope="col">No</th>
                        <th class="px-2 py-2 text-left font-bold uppercase tracking-wider text-slate-600"
                            scope="col">Nama</th>
                        <th class="px-2 py-2 text-left font-bold uppercase tracking-wider text-slate-600"
                            scope="col">No. Handphone</th>
                        <th class="px-2 py-2 text-left font-bold uppercase tracking-wider text-slate-600"
                            scope="col">Email</th>
                        <th class="px-2 py-2 text-left font-bold uppercase tracking-wider text-slate-600"
                            scope="col">Jenis Hadiah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach ($chunk as $index => $winner)
                        <tr class="break-inside-avoid transition-colors duration-200 hover:bg-slate-50">
                            <td class="text-cedea-blue whitespace-nowrap py-1 font-bold">
                                {{ $index + 1 }}
                            </td>
                            <td class="wrap-normal flex flex-col items-start px-2 py-1 font-medium text-gray-900">
                                <span class="">{{ $winner['name'] }}</span>
                                <span class="text-cedea-blue text-[.6rem]">
                                    {{ $winner['raffle_number'] }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-2 py-1 text-gray-700">
                                {{ $winner['phone'] }}
                            </td>
                            <td class="whitespace-nowrap px-2 py-1 text-gray-700">
                                {{ $winner['email'] }}
                            </td>
                            <td class="whitespace-nowrap px-2 py-1">
                                <span
                                    class="text-cedea-blue inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-bold">
                                    {{ $winner['prize']->label() }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if (!$loop->last)
            @pageBreak
        @endif
    @endforeach

    <div class="justify-self-end-safe flex flex-col pt-12">
        @php
            $signatures = [
                'Kementerian Sosial RI',
                'Kementerian Sosial RI',
                'Dinas Sosial Provinsi DKI Jakarta',
                'Dinas Sosial Provinsi DKI Jakarta',
                'Notaris',
                'Agency',
                'Penyelenggara',
                'Penyelenggara',
            ];
            $chunkedSignatures = array_chunk($signatures, 4);
        @endphp

        @foreach ($chunkedSignatures as $row)
            <div class="grid grid-cols-4">
                @foreach ($row as $signature)
                    <div class="border border-gray-800 p-0 text-center align-top">
                        <div
                            class="flex h-16 items-center justify-center border-b border-gray-800 bg-slate-50 px-2 py-3 text-xs font-black uppercase">
                            {{ $signature }}
                        </div>
                        <div class="h-32 bg-white"></div>
                    </div>
                @endforeach

                @for ($i = count($row); $i < 4; $i++)
                    <div class="border border-gray-800 bg-slate-50 p-0 text-center align-top"></div>
                @endfor
            </div>
        @endforeach
    </div>

</body>

</html>
