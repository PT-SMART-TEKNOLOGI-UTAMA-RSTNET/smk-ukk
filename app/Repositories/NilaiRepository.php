<?php
/**
 * Created by PhpStorm.
 * User: reyang
 * Date: 2/11/2022
 * Time: 10:57 PM
 */

namespace App\Repositories;


use App\Models\CapaianKeterampilan;
use App\Models\CapaianPengetahuan;
use App\Models\Jurusan;
use App\Models\KeterampilanIndikator;
use App\Models\KeterampilanKomponen;
use App\Models\PengetahuanIndikator;
use App\Models\PengetahuanKomponen;
use App\Models\Peserta;
use App\Models\SikapCapaian;
use App\Models\SikapIndikator;
use App\Models\Ujian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class NilaiRepository
{
    protected $userRepository;
    public function __construct()
    {
        $this->userRepository = new AuthRepository();
    }

    public function pengetahuanSave(Request $request) {
        try {
            $peserta = Peserta::where('id', $request->peserta)->first();
            $soal = PengetahuanKomponen::where('id', $request->soal)->first();
            $capaian = CapaianPengetahuan::where('ujian', $peserta->ujian)
                ->where('peserta', $peserta->id)
                ->where('komponen', $soal->id)->get();
            if ($capaian->count() === 0) {
                $capaian = new CapaianPengetahuan();
                $capaian->id = Uuid::uuid4()->toString();
                $capaian->ujian = $peserta->ujian;
                $capaian->peserta = $peserta->id;
                $capaian->komponen = $soal->id;
            } else {
                $capaian = $capaian->first();
            }
            $capaian->lock = true;
            $capaian->nilai = $request->nilai;
            $capaian->indikator = $soal->answer;
            $capaian->saveOrFail();
            return $capaian;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function cetakKartuUjian(Request $request){
        try {
            return $this->table($request);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function downloadNilai(Request $request){
        try {
            $ujian = Ujian::where('id', $request->id)->first();
            $jurusan = JurusanRepository::getJurusan(new Request(['id' => $ujian->jurusan]))->first();
            $pesertas = Peserta::where('ujian', $request->id)->orderBy('nopes','asc')->get(['id','user','paket','ujian','nopes']);

            if ($pesertas->count() == 0) throw new \Exception('Tidak ada data peserta',400);

            $inputFileName = public_path() . '/format/download_nilai.xlsx';
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
            $sheet = $spreadsheet->getSheetByName('input');
            $sheet->setCellValue("D2", ": SMK Muhammadiyah Kandanghaur");
            $sheet->setCellValue("D4",": " . Carbon::now()->translatedFormat('d F Y, H:i'));
            $sheet->setCellValue("D5", ": " . $jurusan['label']);
            $sheet->setCellValue("D6",": " . Carbon::parse($ujian->start_at)->translatedFormat('d F Y') . ' s/d ' . Carbon::parse($ujian->end_at)->translatedFormat('d F Y'));
            $row = 10;
            foreach ($pesertas as $index => $peserta){
                ini_set('max_execution_time',0);
                $user = $peserta->userObj;
                $paket = $peserta->paketObj;
                $sheet->setCellValue("A$row",$index + 1);
                $sheet->setCellValue("B$row", $user->nis);
                $sheet->setCellValue("C$row", $peserta->nopes);
                $sheet->setCellValue("D$row", $user->name);
                $sheet->setCellValue("E$row", $paket->name);
                $nilaiPengetahuan = CapaianPengetahuan::where('peserta', $peserta->id)->where('ujian', $ujian->id)->sum('nilai') / PengetahuanKomponen::where('paket', $peserta->paket)->count();
                $sheet->setCellValue("F$row", round($nilaiPengetahuan));

                $nilaiKeterampilan = 0;
                $komponenKeterampilans = KeterampilanKomponen::where('paket', $peserta->paket)->orderBy('nomor','asc')->get(['id','nilai_sangat_baik','nilai_baik','nilai_cukup','nilai_tidak']);
                foreach ($komponenKeterampilans as $komponenKeterampilan){
                    $capaianIndikator = 0;
                    $indikators = KeterampilanIndikator::where('komponen', $komponenKeterampilan->id)->orderBy('nomor','asc')->get(['id']);
                    foreach ($indikators as $indikator){
                        $capaian = CapaianKeterampilan::where('indikator', $indikator->id)->where('peserta', $peserta->id)->where('ujian', $ujian->id)->get('nilai');
                        if ($capaian->count() > 0) {
                            $capaianIndikator = $capaianIndikator + $capaian->first()->nilai;
                        }
                    }
                    if ($capaianIndikator >= $komponenKeterampilan->nilai_sangat_baik) {
                        $nilaiKeterampilan = $nilaiKeterampilan + 3;
                    } elseif ($capaianIndikator >= $komponenKeterampilan->nilai_baik && $capaianIndikator < $komponenKeterampilan->nilai_cukup) {
                        $nilaiKeterampilan = $nilaiKeterampilan + 2;
                    } elseif ($capaianIndikator >= $komponenKeterampilan->nilai_cukup && $capaianIndikator < $komponenKeterampilan->nilai_tidak) {
                        $nilaiKeterampilan = $nilaiKeterampilan + 1;
                    }
                }
                if ($nilaiKeterampilan == 0) {
                    $sheet->setCellValue("G$row", 60);
                } elseif ($nilaiKeterampilan == 1) {
                    $sheet->setCellValue("G$row", 70);
                } elseif ($nilaiKeterampilan == 2) {
                    $sheet->setCellValue("G$row", 85);
                } elseif ($nilaiKeterampilan == 4) {
                    $sheet->setCellValue("G$row", 100);
                }
                $sheet->setCellValue("H$row","=(F$row*30%)+(G$row*70%)");
                $row++;
            }
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="NILAI-UKK-'.$jurusan['label'].'.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function cetakLembarNilai(Request $request) {
        try {
            $peserta = $this->table($request)->first();
            return $peserta;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function create(Request $request) {
        try {
            $response = null;
            $currentUser = auth()->guard('api')->user();
            $checkCapaian = CapaianKeterampilan::where('peserta', $request->peserta)
                ->where('indikator', $request->indikator)
                ->where('ujian', $request->ujian)->get();
            if ($checkCapaian->count() === 0) {
                $capaian = new CapaianKeterampilan();
                $capaian->id = Uuid::uuid4()->toString();
                $capaian->indikator = $request->indikator;
                $capaian->peserta = $request->peserta;
                $capaian->ujian = $request->ujian;
                $capaian->created_by = $currentUser->id;
            } else {
                $capaian = $checkCapaian->first();
                $capaian->updated_by = $currentUser->id;
            }
            $capaian->nilai = $request->nilai == 'yes' ? 1 : 0;
            $capaian->saveOrFail();
            $response = ['value' => $capaian->id, 'label' => $capaian->nilai];
            return $response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    private function nilaiKomponenKeterampilan(Peserta $peserta, $komponen) {
        try {
            $response = collect([]);
            $persiapan = KeterampilanKomponen::where('paket', $peserta->paket)->where('komponen',$komponen)->orderBy('nomor','asc')->get();
            foreach ($persiapan as $item){
                $jml_capaian = $nilai_akhir = 0;
                $indikatorCollection = collect([]);
                $indikators = KeterampilanIndikator::where('komponen', $item->id)->orderBy('nomor','asc')->get();
                foreach ($indikators as $indexIndikator => $indikator){
                    $indikator->capaian = CapaianKeterampilan::where('indikator', $indikator->id)
                        ->where('peserta', $peserta->id)->where('ujian', $peserta->ujian)->first();
                    if ($indikator->capaian != null){
                        $jml_capaian += $indikator->capaian->nilai;
                    }
                    $indikatorCollection->push($indikator);
                }
                if ($jml_capaian >= $item->nilai_cukup && $jml_capaian < $item->nilai_baik){
                    $nilai_akhir = 1;
                } elseif ($jml_capaian >= $item->nilai_baik && $jml_capaian < $item->nilai_sangat_baik) {
                    $nilai_akhir = 2;
                } elseif ($jml_capaian >= $item->nilai_sangat_baik) {
                    $nilai_akhir = 3;
                }
                $response->push([
                    'value' => $item->id,
                    'label' => $item->name,
                    'meta' => [
                        'nomor' => $item->nomor,
                        'nilai' => [
                            'indikator' => $indikatorCollection,
                            'jml_indikator' => $indikators->count(),
                            'jml_capaian' => $jml_capaian,
                            'nilai_akhir' => $nilai_akhir
                        ],
                        'target_capaian' => [
                            'sb' => $item->nilai_sangat_baik,
                            'b' => $item->nilai_baik,
                            'c' => $item->nilai_cukup,
                            't' => $item->nilai_tidak
                        ]
                    ]
                ]);
            }
            return $response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    private function nilaiKonversi($nilai){
        try {
            $response = 0;
            switch ($nilai){
                case 0 : $response = 60; break;
                case 1 : $response = 70; break;
                case 2 : $response = 85; break;
                case 3 : $response = 100; break;
            }
            return $response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    private function nilaiKeterampilan(Peserta $peserta){
        try {
            $response = new \StdClass();
            $response->komponen = new \StdClass();
            $response->komponen->persiapan = $this->nilaiKomponenKeterampilan($peserta,'persiapan');
            $response->komponen->pelaksanaan = $this->nilaiKomponenKeterampilan($peserta,'pelaksanaan');
            $response->komponen->hasil = $this->nilaiKomponenKeterampilan($peserta,'hasil');
            $response->nilai = [
                'nilai_persiapan' => $response->komponen->persiapan->sum('meta.nilai.nilai_akhir') / $response->komponen->persiapan->count(),
                'nilai_pelaksanaan' => $response->komponen->pelaksanaan->sum('meta.nilai.nilai_akhir') / $response->komponen->pelaksanaan->count(),
                'nilai_hasil' => $response->komponen->hasil->sum('meta.nilai.nilai_akhir') / $response->komponen->hasil->count(),
                'nilai_total' => 0
            ];
            $response->nilai['nilai_total'] = round(($response->nilai['nilai_persiapan'] + $response->nilai['nilai_pelaksanaan'] + $response->nilai['nilai_hasil']) / 3);
            $response->nilai['konversi'] = $this->nilaiKonversi($response->nilai['nilai_total']);
            return $response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    private function nilaiSoal(Peserta $peserta){
        try {
            $response = new \StdClass();
            $response->komponen = new \StdClass();
            $response->komponen->soal = collect([]);
            $response->nilai = [ 'konversi' => 0 ];
            $total_capaian = 0;
            $soals = PengetahuanKomponen::orderBy('nomor','asc')->where('paket', $peserta->paket)->get();
            foreach ($soals as $soal){
                $jml_capaian = 0;
                $indikator = collect([]);
                $dataIndikators = PengetahuanIndikator::where('komponen', $soal->id)->orderBy('nomor', 'asc')->get();
                foreach ($dataIndikators as $dataIndikator){
                    $indikator->push([
                        'value' => $dataIndikator->id,
                        'label' => $dataIndikator->content,
                        'meta' => [
                            'nomor' => $dataIndikator->nomor
                        ]
                    ]);
                }
                $dataCapaian = CapaianPengetahuan::where('komponen', $soal->id)->where('peserta', $peserta->id)->where('ujian', $peserta->ujian)->get();
                if ($dataCapaian->count() > 0) {
                    $dataCapaian = $dataCapaian->first();
                    if ($soal->type == 'pg'){
                        if ($dataCapaian->indikator == $soal->answer){
                            $dataCapaian->nilai = 100;
                            $dataCapaian->saveOrFail();
                        }
                        $jml_capaian = $dataCapaian->nilai;
                    } elseif ($soal->type == 'essay') {
                        $jml_capaian = $dataCapaian->nilai;
                        /*$explodeJawaban = explode(' ', $dataCapaian->answer_content);
                        $capaianIndikator = PengetahuanIndikator::where('komponen', $soal->id);
                        foreach ($explodeJawaban as $key => $item){
                            if ($key == 0) {
                                $capaianIndikator = $capaianIndikator->where('content', 'like', "$$item");
                            } else {
                                $capaianIndikator = $capaianIndikator->orWhere('content', 'like', "%$item%");
                            }
                        }
                        $capaianIndikator = $capaianIndikator->get('id');*/
                        /*$jml_capaian = ( $capaianIndikator->count() * 100 ) / $dataIndikators->count();*/
                        /*if (!$dataCapaian->lock){
                            $dataCapaian->nilai = $jml_capaian;
                            $dataCapaian->saveOrFail();
                        }*/
                    }
                }
                $total_capaian += $jml_capaian;
                $response->komponen->soal->push([
                    'value' => $soal->id,
                    'label' => $soal->content,
                    'meta' => [
                        'nomor' => $soal->nomor,
                        'elemen' => $soal->elemen_kompetensi,
                        'nilai' => [
                            'indikator' => $indikator,
                            'jml_indikator' => $indikator->count(),
                            'jml_capaian' => $jml_capaian,
                            'nilai_akhir' => $jml_capaian
                        ]
                    ]
                ]);
            }
            if ($total_capaian > 0) {
                $konversi = round($total_capaian / $soals->count());
            } else {
                $konversi = 0;
            }
            $response->nilai['konversi'] = $konversi;
            return $response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    private function nilaiPengetahuan(Peserta $peserta){
        try {
            $response = $this->nilaiSoal($peserta);
            return $response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    private function nilaiSikap(Peserta $peserta){
        try {
            $response = new \StdClass();
            $response->komponen = new \StdClass();
            $response->komponen->sikap = collect([]);
            $komponenSikaps = SikapIndikator::where('paket', $peserta->paket)->orderBy('indikator','asc')->get();
            $total_nilai = 0;
            foreach($komponenSikaps as $index => $komponenSikap){
                $capaianSikap = SikapCapaian::where('ujian', $peserta->ujian)->where('indikator', $komponenSikap->id)
                    ->where('peserta', $peserta->id)->get();
                if ($capaianSikap->count() == 0) {
                    $capaianSikap = new SikapCapaian();
                    $capaianSikap->id = Uuid::uuid4()->toString();
                    $capaianSikap->indikator = $komponenSikap->id;
                    $capaianSikap->peserta = $peserta->id;
                    $capaianSikap->ujian = $peserta->ujian;
                } else {
                    $capaianSikap = $capaianSikap->first();
                }
                $capaianSikap->nilai = 100;
                $capaianSikap->saveOrFail();
                $response->komponen->sikap->push([
                    'value' => $komponenSikap->id,
                    'label' => $komponenSikap->indikator,
                    'meta' => [
                        'nomor' => $index + 1,
                        'nilai' => [
                            'indikator' => collect([]),
                            'jml_indikator' => 1,
                            'jml_capaian' => 100,
                            'nilai_akhir' => 100
                        ]
                    ]
                ]);
                $total_nilai += $capaianSikap->nilai;
            }
            if ($total_nilai > 0) {
                $konversi = $total_nilai / $komponenSikaps->count();
            } else {
                $konversi = 0;
            }
            $response->nilai = [
                'konversi' => $konversi
            ];
            return $response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    private function generateNilai(Peserta $peserta)
    {
        try {
            //$response = new \StdClass();
            $response = collect([]);
            $response->push([
                'label' => 'Keterampilan',
                'value' => $this->nilaiKeterampilan($peserta)
            ]);
            $response->push([
                'label' => 'Pengetahuan',
                'value' => $this->nilaiPengetahuan($peserta)
            ]);
            $response->push([
                'label' => 'Sikap',
                'value' => $this->nilaiSikap($peserta)
            ]);
            //$response->keterampilan = $this->nilaiKeterampilan($peserta);
            //$response->sikap = collect([]);
            //$response->pengetahuan = collect([]);
            return $response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), 500);
        }
    }
    public function table(Request $request){
        try{
            $response = collect([]);
            $pesertas = Peserta::orderBy('nopes','asc');
            if (strlen($request->ujian) > 0) $pesertas = $pesertas->where('ujian', $request->ujian);
            if (strlen($request->peserta) > 0) $pesertas = $pesertas->where('id', $request->peserta);
            $pesertas = $pesertas->get();
            foreach ($pesertas as $peserta){
                $user = $peserta->userObj;
                $response->push([
                    'value' => $peserta->id,
                    'label' => $user->name,
                    'meta' => [
                        'user' => $this->userRepository->table(new Request(['id' => $peserta->user]))->first(),
                        'nopes' => $peserta->nopes,
                        'nilai' => $this->generateNilai($peserta),
                        'paket' => $peserta->paket,
                        'ujian' => $peserta->ujian
                    ]
                ]);
            }
            return $response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
}