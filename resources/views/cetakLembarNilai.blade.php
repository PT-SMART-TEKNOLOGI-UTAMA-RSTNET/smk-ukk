<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Lembar Penilaian</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cetak.min.css') }}">
    <style>
        * {
            font-family: 'Roboto Slab', serif !important; font-size:10pt !important;
        }
        .it-grid tr:nth-child(2n){
            background:white;
        }
        .it-grid th{
            background:white;
        }
        .besar td{
            font-size:10pt !important;
        }
    </style>
</head>
<body>
    @php $explodeNopes = str_split($peserta['meta']['nopes']) @endphp
    <div class="page">
        <table width="50%" class="it-grid">
            <tr>
                <td>No. Peserta</td>
                @foreach($explodeNopes as $nopes)
                    <td width="20px" align="center" valign="middle">{{$nopes}}</td>
                @endforeach
            </tr>
        </table>
        <table width="100%">
            <tr>
                <td width="50%" valign="top">
                    <b style="border:solid 1px black;padding:10px;margin-top:10px;display:inline-block">DOKUMEN NEGARA</b>
                </td>
                <td valign="top" align="right">
                    <b style="text-align:center;border:solid 1px black;padding:10px;margin-top:10px;display:inline-block">PAKET<br>1</b>
                </td>
            </tr>
        </table>
        <div style="text-align:center;font-weight:bold;font-size:12pt !important;">
            UJI KOMPETENSI KEAHLIAN<br>
            TAHUN PELAJARAN 2021/2022<br>&nbsp;<br>
            LEMBAR PENILAIAN
        </div>
        <table width="50%" style="margin:10px auto" class="besar">
            <tr>
                <td>Satuan Pendidikan</td>
                <td width="2px">:</td>
                <td>Sekolah Menengah Kejuruan</td>
            </tr>
            <tr>
                <td>Kompetensi Keahlian</td>
                <td>:</td>
                <td>{{$peserta['meta']['user']['meta']['jurusan']['label']}}</td>
            </tr>
            <tr>
                <td>Alokasi Waktu</td>
                <td>:</td>
                <td>8 Jam</td>
            </tr>
            <tr>
                <td>Bentuk Soal</td>
                <td>:</td>
                <td>Penugasan Perorangan</td>
            </tr>
            <tr>
                <td>Judul Tugas</td>
                <td>:</td>
                <td>{{$peserta['meta']['paket']['label']}}</td>
            </tr>
        </table>
        <div style="height:5px;border-bottom:solid 2px black;border-top:solid 1px black"></div>
        <table width="100%" style="margin:10px auto">
            <tr>
                <td width="150px">Nama Peserta</td>
                <td width="2px">:</td>
                <td>{{$peserta['meta']['user']['label']}}</td>
            </tr>
        </table>
        <b><u>Form Penilaian Aspek Pengetahuan</u></b>
        <table width="100%" class="it-grid">
            <tr>
                <th rowspan="2" class="align-middle text-center">Elemen Kompetensi</th>
                <th colspan="2" class="align-middle text-center">Metode</th>
                <th colspan="2" class="align-middle text-center">Jawaban</th>
                <th rowspan="2" class="align-middle text-center" width="70px">Skor</th>
            </tr>
            <tr>
                <th width="50px" class="align-middle text-center">Tes Tulis</th>
                <th width="50px" class="align-middle text-center">Tes Lisan</th>
                <th width="50px" class="align-middle text-center">Benar</th>
                <th width="50px" class="align-middle text-center">Salah</th>
            </tr>
            <tr>
                @for($i = 1; $i <= 6; $i++)
                    <th class="align-middle text-center">{{$i}}</th>
                @endfor
            </tr>
            @forelse($peserta['meta']['nilai'][1]['value']->komponen->soal as $soal)
                <tr>
                    <td>{{$soal['meta']['elemen']}}</td>
                    <td class="text-center align-middle">&check;</td><td> </td>
                    <td class="text-center align-middle">@if($soal['meta']['nilai']['nilai_akhir'] > 0) &check; @endif</td>
                    <td class="text-center align-middle">@if($soal['meta']['nilai']['nilai_akhir'] == 0) &check; @endif</td>
                    <td class="text-center align-middle">{{$soal['meta']['nilai']['nilai_akhir']}}</td>
                </tr>
            @empty
            @endforelse
            <tr>
                <th class="align-middle text-right">Nilai Pengetahuan</th>
                <th colspan="5" class="align-middle text-center">{{$peserta['meta']['nilai'][1]['value']->nilai['konversi']}}</th>
            </tr>
        </table>
        <ul style="margin:10px">
            <li>Asesor/Penguji mengembangkan butir tes pengetahuan berdasarkan Indikator Pencapaian Kompetensi</li>
            <li>Bobot penilaian per butir soal ditentukan oleh asesor/penguji</li>
            <li>Nilai Pengetahuan merupakan pembulatan hasil pengolahan penskoran jawaban benar (skor hasil perolehan/skor maksimal x 100)</li>
        </ul>
        <table width="100%" style="margin-top:10px">
            <tr>
                <td align="right" valign="top">
                    Kandanghaur, {{\Carbon\Carbon::parse($peserta['meta']['ujian']['meta']['tanggal']['mulai'])->translatedFormat('d F Y')}}<br>
                    Penilai 1 / Penilai 2
                    <div style="width:200px;height:100px;border-bottom:dashed 1px black"></div>
                </td>
            </tr>
        </table>
    </div>
    <div class="page">
        <table width="50%" class="it-grid">
            <tr>
                <td>No. Peserta</td>
                @foreach($explodeNopes as $nopes)
                    <td width="20px" align="center" valign="middle">{{$nopes}}</td>
                @endforeach
            </tr>
        </table>
        <b><u>Form Penilaian Aspek Pengetahuan</u></b>
        <table width="100%" class="it-grid" style="margin:10px auto">
            <tr>
                <th rowspan="4" width="40px" class="align-middle text-center">No</th>
                <th rowspan="4" class="align-middle text-center">Komponen / Sub Komponen</th>
                <th colspan="4" class="align-middle text-center">Kompeten</th>
                <th rowspan="4" class="align-middle text-center" width="100px">Catatan</th>
            </tr>
            <tr>
                <th rowspan="2" class="align-middle text-center" width="50px">Belum</th>
                <th colspan="3" class="align-middle text-center">Ya</th>
            </tr>
            <tr>
                <th class="align-middle text-center" width="50px">Cukup</th>
                <th class="align-middle text-center" width="50px">Baik</th>
                <th class="align-middle text-center" width="50px">Sangat Baik</th>
            </tr>
            <tr>
                @for($i = 0; $i <= 3; $i++)
                    <th class="align-middle text-center">{{$i}}</th>
                @endfor
            </tr>
            <tr>
                @for($i = 1; $i <= 7; $i++)
                    <th class="align-middle text-center">{{$i}}</th>
                @endfor
            </tr>
            <tr style="background:whitesmoke">
                <td class="align-middle text-center"><b>I</b></td>
                <td colspan="6" class="align-middle"><b>Persiapan</b></td>
            </tr>
            @forelse($peserta['meta']['nilai'][0]['value']->komponen->persiapan as $item)
                <tr>
                    <td class="align-middle text-center">1.{{$item['meta']['nomor']}}</td>
                    <td class="align-middle">{{$item['label']}}</td>
                    <td class="align-middle text-center">
                        @if($item['meta']['nilai']['jml_capaian'] == $item['meta']['target_capaian']['t']) &check; @endif
                    </td>
                    <td class="align-middle text-center">
                        @if($item['meta']['nilai']['jml_capaian'] == $item['meta']['target_capaian']['c']) &check; @endif
                    </td>
                    <td class="align-middle text-center">
                        @if($item['meta']['nilai']['jml_capaian'] == $item['meta']['target_capaian']['b']) &check; @endif
                    </td>
                    <td class="align-middle text-center">
                        @if($item['meta']['nilai']['jml_capaian'] == $item['meta']['target_capaian']['sb']) &check; @endif
                    </td>
                    <td class="align-middle text-center"> </td>
                </tr>
            @empty
            @endforelse
            <tr>
                <td colspan="2" class="align-middle"><b>Rerata Komponen Persiapan (Pembulatan)</b></td>
                <td colspan="5" class="align-middle text-center"><b>{{$peserta['meta']['nilai'][0]['value']->nilai['nilai_persiapan']}}</b></td>
            </tr>
            <tr style="background:whitesmoke">
                <td class="align-middle text-center"><b>II</b></td>
                <td colspan="6" class="align-middle"><b>Pelaksanaan</b></td>
            </tr>
            @forelse($peserta['meta']['nilai'][0]['value']->komponen->pelaksanaan as $item)
                <tr>
                    <td class="align-middle text-center">2.{{$item['meta']['nomor']}}</td>
                    <td class="align-middle">{{$item['label']}}</td>
                    <td class="align-middle text-center">
                        @if($item['meta']['nilai']['jml_capaian'] == $item['meta']['target_capaian']['t']) &check; @endif
                    </td>
                    <td class="align-middle text-center">
                        @if($item['meta']['nilai']['jml_capaian'] == $item['meta']['target_capaian']['c']) &check; @endif
                    </td>
                    <td class="align-middle text-center">
                        @if($item['meta']['nilai']['jml_capaian'] == $item['meta']['target_capaian']['b']) &check; @endif
                    </td>
                    <td class="align-middle text-center">
                        @if($item['meta']['nilai']['jml_capaian'] == $item['meta']['target_capaian']['sb']) &check; @endif
                    </td>
                    <td class="align-middle text-center"> </td>
                </tr>
            @empty
            @endforelse
            <tr>
                <td colspan="2" class="align-middle"><b>Rerata Komponen Pelaksanaan (Pembulatan)</b></td>
                <td colspan="5" class="align-middle text-center"><b>{{$peserta['meta']['nilai'][0]['value']->nilai['nilai_pelaksanaan']}}</b></td>
            </tr>
            <tr style="background:whitesmoke">
                <td class="align-middle text-center"><b>III</b></td>
                <td colspan="6" class="align-middle"><b>Hasil</b></td>
            </tr>
            @forelse($peserta['meta']['nilai'][0]['value']->komponen->hasil as $item)
                <tr>
                    <td class="align-middle text-center">1.{{$item['meta']['nomor']}}</td>
                    <td class="align-middle">{{$item['label']}}</td>
                    <td class="align-middle text-center">
                        @if($item['meta']['nilai']['jml_capaian'] == $item['meta']['target_capaian']['t']) &check; @endif
                    </td>
                    <td class="align-middle text-center">
                        @if($item['meta']['nilai']['jml_capaian'] == $item['meta']['target_capaian']['c']) &check; @endif
                    </td>
                    <td class="align-middle text-center">
                        @if($item['meta']['nilai']['jml_capaian'] == $item['meta']['target_capaian']['b']) &check; @endif
                    </td>
                    <td class="align-middle text-center">
                        @if($item['meta']['nilai']['jml_capaian'] == $item['meta']['target_capaian']['sb']) &check; @endif
                    </td>
                    <td class="align-middle text-center"> </td>
                </tr>
            @empty
            @endforelse
            <tr>
                <td colspan="2" class="align-middle"><b>Rerata Komponen Hasil (Pembulatan)</b></td>
                <td colspan="5" class="align-middle text-center"><b>{{$peserta['meta']['nilai'][0]['value']->nilai['nilai_hasil']}}</b></td>
            </tr>
        </table>
    </div>
    <div class="page">
        <table width="50%" class="it-grid">
            <tr>
                <td>No. Peserta</td>
                @foreach($explodeNopes as $nopes)
                    <td width="20px" align="center" valign="middle">{{$nopes}}</td>
                @endforeach
            </tr>
        </table>
        <div style="margin:10px auto">Keterangan:</div>
        <ul style="margin:10px">
            <li>Capaian kompetensi peserta uji per Sub Komponen dituliskan dalam bentuk ceklis (√) atau skor 0, 1, 2, atau 3</li>
            <li>Rerata komponen peserta uji per Komponen dituliskan dalam bentuk skor berbentuk bilangan bulat</li>
            <li>Peserta uji dapat diberi kesempatan untuk mengulang</li>
            <li>Catatan negatif diberikan kepada peserta uji yang mengulangi proses atau unjuk kerja lainnya yang bertentangan dengan kriteria unjuk kerja</li>
        </ul>
        <b><u>Rekapitulasi Penilaian Aspek Keterampilan</u></b><br>
        <table width="40%" class="it-grid" style="margin:10px 0">
            <tr>
                <td class="align-middle text-center"> </td>
                <td class="align-middle text-center" width="100px"><b>Jumlah Catatan</b></td>
            </tr>
            <tr>
                <td class="align-middle text-center">Catatan Negatif</td><td> </td>
            </tr>
            <tr>
                <td class="align-middle text-center"><b>Pengurangan Nilai</b></td>
            </tr>
        </table>
        <div style="margin:10px auto">Keterangan:</div>
        <ul style="margin:10px">
            <li>Nilai tambahan diberikan berdasarkan penjumlahan dari catatan positif (bernilai positif) dan catatan negatif (bernilai negatif) dengan maksimal 10 poin dan minimal -10 poin</li>
        </ul>
        <table width="100%" style="margin:10px 0" class="it-grid">
            <tr>
                <th rowspan="3"> </th>
                <th colspan="4" class="align-middle text-center">Tingkat Pencapaian Kompetensi</th>
                <th rowspan="2" class="align-middle text-center" width="100px">Tingkat Perolehan (Hasil Konversi)</th>
                <th rowspan="2" class="align-middle text-center" width="100px">Pengurangan Nilai</th>
                <th rowspan="2" class="align-middle text-center" width="100px">Nilai Akhir Aspek Keterampilan</th>
            </tr>
            <tr>
                <th colspan="3" class="align-middle text-center">Keterampilan</th>
                <th class="align-middle text-center">Skor Awal (Pembulatan)</th>
            </tr>
            <tr>
                <th class="align-middle text-center" width="100px">Persiapan</th>
                <th class="align-middle text-center" width="100px">Pelaksanaan</th>
                <th class="align-middle text-center" width="100px">Hasil</th>
                <th rowspan="4" class="align-middle text-center">{{$peserta['meta']['nilai'][0]['value']->nilai['konversi']}}</th>
                <th rowspan="4" class="align-middle text-center">{{$peserta['meta']['nilai'][0]['value']->nilai['konversi']}}</th>
                <th rowspan="4" class="align-middle text-center">0</th>
                <th rowspan="4" class="align-middle text-center">{{$peserta['meta']['nilai'][0]['value']->nilai['konversi']}}</th>
            </tr>
            <tr>
                <td class="align-middle text-center">Nilai Rata-rata (Pembulatan)</td>
                <td class="align-middle text-center">{{$peserta['meta']['nilai'][0]['value']->nilai['nilai_persiapan']}}</td>
                <td class="align-middle text-center">{{$peserta['meta']['nilai'][0]['value']->nilai['nilai_pelaksanaan']}}</td>
                <td class="align-middle text-center">{{$peserta['meta']['nilai'][0]['value']->nilai['nilai_hasil']}}</td>
            </tr>
            <tr>
                <td class="align-middle text-center">Bobot</td>
                <td class="align-middle text-center">1</td>
                <td class="align-middle text-center">1</td>
                <td class="align-middle text-center">1</td>
            </tr>
            <tr>
                <td class="align-middle text-center">Nilai Komponen</td>
                <td class="align-middle text-center">{{$peserta['meta']['nilai'][0]['value']->nilai['nilai_persiapan']}}</td>
                <td class="align-middle text-center">{{$peserta['meta']['nilai'][0]['value']->nilai['nilai_pelaksanaan']}}</td>
                <td class="align-middle text-center">{{$peserta['meta']['nilai'][0]['value']->nilai['nilai_hasil']}}</td>
            </tr>
        </table>
        <div style="margin:10px auto">Keterangan:</div>
        <ul style="margin:10px">
            <li>Nilai rata-rata diperoleh dari lembar penilaian (Tidak = 0; Cukup = 1; Baik = 2; Sangat Baik = 3)</li>
            <li>Bobot ditetapkan secara terpusat dan dapat berubah sesuai persetujuan dengan industri (dibuktikan dengan berita acara)</li>
            <li>Nilai Komponen diperoleh dari hasil perkalian Nilai rata-rata dengan Bobot</li>
            <li>Skor Awal diperoleh dari pembulatan hasil penjumlahan komponen Persiapan, Pelaksanaan, dan Hasil</li>
            <li>Nilai Perolehan diperoleh dari nilai maksimal hasil konversi skor awal</li>
            <li>Pengurangan Nilai diperoleh dari catatan yang diperoleh dan dittulis dengan tanda minus</li>
            <li>Nilai Akhir diperoleh dari penjumlahan Nilai Perolehan dengan Pengurangan Nilai</li>
        </ul>
        <b>Konversi Nilai</b>
        <table width="50%" class="it-grid" style="margin:10px 0">
            <tr>
                <th class="align-middle text-center">Skor Awal</th>
                <th class="align-middle text-center">Nilai Konversi</th>
            </tr>
            <tr>
                <td class="align-middle text-center">0</td><td class="align-middle text-center">< 61</td>
                <td class="align-middle text-center">1</td><td class="align-middle text-center">61-70</td>
                <td class="align-middle text-center">2</td><td class="align-middle text-center">71-85</td>
                <td class="align-middle text-center">3</td><td class="align-middle text-center">86-100</td>
            </tr>
        </table>
    </div>
    <div class="page">
        <table width="50%" class="it-grid">
            <tr>
                <td>No. Peserta</td>
                @foreach($explodeNopes as $nopes)
                    <td width="20px" align="center" valign="middle">{{$nopes}}</td>
                @endforeach
            </tr>
        </table>
        <b><u>Nilai Akhir</u></b>
        <table width="70%" class="it-grid" style="margin:10px 0">
            <tr>
                <th> </th>
                <th class="align-middle text-center" width="100px">Aspek Pengetahuan</th>
                <th class="align-middle text-center" width="100px">Aspek Keterampilan</th>
                <th class="align-middle text-center" width="100px">Nilai Akhir (Pembulatan)</th>
            </tr>
            <tr>
                <td class="align-middle text-center">Nilai Perolehan</td>
                <td class="align-middle text-center"> {{$peserta['meta']['nilai'][1]['value']->nilai['konversi']}}</td>
                <td class="align-middle text-center"> {{$peserta['meta']['nilai'][0]['value']->nilai['konversi']}}</td>
                <td class="align-middle text-center" rowspan="3">
                    {{
                    round(
                    (($peserta['meta']['nilai'][0]['value']->nilai['konversi']*70)/100)
                    +
                    (($peserta['meta']['nilai'][1]['value']->nilai['konversi']*30)/100)
                    )
                    }}
                </td>
            </tr>
            @php $nilaiAkhir = round(
                    (($peserta['meta']['nilai'][0]['value']->nilai['konversi']*70)/100)
                    +
                    (($peserta['meta']['nilai'][1]['value']->nilai['konversi']*30)/100)
                    )
            @endphp
            <tr>
                <td class="align-middle text-center">Bobot</td>
                <td class="align-middle text-center">30%</td>
                <td class="align-middle text-center">70%</td>
            </tr>
            <tr>
                <td class="align-middle text-center">Nilai Perolehan</td>
                <td class="align-middle text-center">{{round(($peserta['meta']['nilai'][1]['value']->nilai['konversi']*30)/100)}}</td>
                <td class="align-middle text-center">{{round(($peserta['meta']['nilai'][1]['value']->nilai['konversi']*70)/100)}}</td>
            </tr>
        </table>
        <div style="margin:10px auto">Keterangan:</div>
        <ul style="margin:10px">
            <li>Nilai rata-rata diperoleh dari lembar penilaian</li>
            <li>Bobot ditetapkan secara terpusat oleh Kementerian Pendidikan dan Kebudayaan dan bersifat mutlak</li>
            <li>Nilai Komponen diperoleh dari hasil perkalian Nilai rata-rata dengan Bobot</li>
            <li>Nilai Akhir berupa bilangan bulat berada pada rentang 0-100 </li>
        </ul>
        <table width="50%" class="it-grid" style="margin:10px 0">
            <tr>
                <th class="align-middle text-center">Nilai Akhir</th>
                <th class="align-middle text-center">Kesimpulan</th>
            </tr>
            <tr>
                <td class="align-middle text-center">< 61</td><td class="align-middle text-center">Belum Kompeten</td>
            </tr>
            <tr>
                <td class="align-middle text-center">61-70</td><td class="align-middle text-center">Cukup Kompeten</td>
            </tr>
            <tr>
                <td class="align-middle text-center">71-85</td><td class="align-middle text-center">Kompeten</td>
            </tr>
            <tr>
                <td class="align-middle text-center">86-100</td><td class="align-middle text-center">Sangat Kompeten</td>
            </tr>
        </table>
        <p>
            Kesimpulan Akhir:
            @if($nilaiAkhir <= 60) Belum Kompeten @else <strike>Belum Kompeten</strike> @endif,
            @if($nilaiAkhir <= 70 && $nilaiAkhir >= 61) Cukup Kompeten @else <strike>Cukup Kompeten</strike> @endif,
            @if($nilaiAkhir <= 85 && $nilaiAkhir >= 71) Kompeten @else <strike>Kompeten</strike> @endif,
            @if($nilaiAkhir >= 86 ) Sangat Kompeten @else <strike>Sangat Kompeten</strike> @endif
            *
        </p>
        <table width="100%" style="margin-top:10px">
            <tr>
                <td valign="bottom">
                    *) Coret yang tidak perlu
                </td>
                <td width="50%" align="right" valign="top">
                    Kandanghaur, {{\Carbon\Carbon::parse($peserta['meta']['ujian']['meta']['tanggal']['mulai'])->translatedFormat('d F Y')}}<br>
                    Penilai 1 / Penilai 2
                    <div style="width:200px;height:100px;border-bottom:dashed 1px black"></div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>