<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Kartu Ujian</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cetak.min.css') }}">
    <style>
        * {
            font-family: 'Roboto Slab', serif !important; font-size:8pt !important;
        }
    </style>
</head>
<body>
    @php $pages = $params->chunk(24) @endphp
    @forelse($pages as $page)
        <div class="page">
            @php $rows = $page->chunk(3) @endphp
            <table width="100%">
                @forelse($rows as $row)
                    <tr>
                        @forelse($row as $peserta)
                            <td width="30%">
                                <div style="border:solid 1px black;padding:10px">
                                    <table width="100%">
                                        <tr>
                                            <td align="center" valign="middle" width="30px"><img src="{{asset('uploads/images/logo.png')}}" width="100%"></td>
                                            <td align="center" valign="middle">
                                                <b>SMK MUHAMMADIYAH KANDANGHAUR</b><br>
                                                <b>KARTU UJI KOMPETENSI</b>
                                            </td>
                                        </tr>
                                    </table>

                                    <div class="text-center" style="border-bottom:solid 1px black;padding-bottom:5px">

                                    </div>
                                    <table width="100%">
                                        <tr>
                                            <td width="50px">Nopes</td><td>:</td>
                                            <td>{{$peserta['meta']['nopes']}}</td>
                                        </tr>
                                        <tr>
                                            <td width="50px">Nama</td>
                                            <td width="3px">:</td>
                                            <td>{{$peserta['label']}}</td>
                                        </tr>
                                        <tr>
                                            <td>Username</td><td>:</td>
                                            <td><b>{{$peserta['meta']['user']['meta']['email']}}</b></td>
                                        </tr>
                                        <tr>
                                            <td>Password</td><td>:</td>
                                            <td><b>{{\Carbon\Carbon::parse($peserta['meta']['user']['meta']['tgl_lahir'])->format('dmY')}}</b></td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        @empty
                        @endforelse
                    </tr>
                @empty
                @endforelse
            </table>
        </div>
    @empty
    @endforelse
</body>
</html>