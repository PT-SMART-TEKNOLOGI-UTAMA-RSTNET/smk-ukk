<?php
/**
 * Created by PhpStorm.
 * User: reyang
 * Date: 2/11/2022
 * Time: 2:53 PM
 */

namespace App\Repositories;


use App\Models\CapaianKeterampilan;
use App\Models\KeterampilanIndikator;
use App\Models\KeterampilanKomponen;
use App\Models\PaketSoal;
use App\Models\Peserta;
use App\Models\Ujian;
use App\Models\User;
use App\Repositories\Komponen\KeterampilanRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader;

class PesertaRepository
{
    protected $komponenKeterampilanRepository;
    protected $packageRepository;
    protected $userRepository;
    public function __construct()
    {
        $this->packageRepository = new PackageRepository();
        $this->userRepository = new AuthRepository();
        $this->komponenKeterampilanRepository = new KeterampilanRepository();
    }

    public function importPeserta(Request $request) {
        try {
            $response = collect([]);
            $file = $request->file('file');
            $ujian = Ujian::where('id', $request->ujian)->first();

            $defaultInternal = User::where('penguji_type', 'internal')->get('id');
            $defaultExternal = User::where('penguji_type', 'external')->get('id');
            if ($defaultInternal->count() > 0) $defaultInternal = $defaultInternal->first();
            if ($defaultExternal->count() > 0) $defaultExternal = $defaultExternal->first();

            $reader = new Reader\Xlsx();
            $spreadsheet = $reader->load($file);
            $sheet = $spreadsheet->setActiveSheetIndexByName('input');
            $highestRow = $sheet->getHighestRow();
            for ($row = 7; $row <= $highestRow; $row++){
                //ini_set('max_execution_time', 500);
                //ini_set('memory_limit', 500);
                $nis = $sheet->getCell("A$row")->getValue();
                $nama = $sheet->getCell("C$row")->getValue();
                if (strlen($nis) > 0 && strlen($nama) > 0) {
                    $siswa = User::where('nis', $nis)->where('jurusan', $ujian->jurusan)->where('tingkat', $ujian->tingkat)->get('id');
                    //$siswa = $this->userRepository->table(new Request(['nis' => $nis]));
                    if ($siswa->count() > 0) {
                        $siswa = $siswa->first();
                        $nopes = $sheet->getCell("B$row")->getValue();
                        if (strlen($nopes) == 0) $nopes = $this->generateNopes(Ujian::where('id', $request->ujian)->first());
                        $namaPaket = $sheet->getCell("D$row")->getValue();
                        $dataPaket = PaketSoal::where('name', $namaPaket)->where('jurusan', $ujian->jurusan)->get('id');
                        if ($dataPaket->count() > 0) {
                            $dataPaket = $dataPaket->first();
                            $internal = $sheet->getCell("E$row")->getValue();
                            $external = $sheet->getCell("F$row")->getValue();
                            if (strlen($internal) > 0) {
                                $pengujiInternal = User::where('name', $internal)->where('penguji_type', 'internal')->get('id');
                                if ($pengujiInternal->count() > 0) {
                                    $pengujiInternal = $pengujiInternal->first()->id;
                                } else {
                                    $pengujiInternal = $defaultInternal->id;
                                }
                            } else {
                                $pengujiInternal = $defaultInternal->id;
                            }
                            if (strlen($external) > 0) {
                                $pengujiExternal = User::where('name', $external)->where('penguji_type', 'external')->get('id');
                                if ($pengujiExternal->count() > 0) {
                                    $pengujiExternal = $pengujiExternal->first()->id;
                                } else {
                                    $pengujiExternal = $defaultExternal->id;
                                }
                            } else {
                                $pengujiExternal = $defaultExternal->id;
                            }
                            $dataPeserta = Peserta::where('user', $siswa->id)->where('ujian', $request->ujian)->get();
                            if ($dataPeserta->count() == 0) {
                                $dataPeserta = new Peserta();
                                $dataPeserta->id = Uuid::uuid4()->toString();
                                $dataPeserta->user = $siswa->id;
                                $dataPeserta->ujian = $request->ujian;
                            } else {
                                $dataPeserta = $dataPeserta->first();
                            }
                            $dataPeserta->paket = $dataPaket->id;
                            $dataPeserta->nopes = $nopes;
                            $dataPeserta->penguji_internal = $pengujiInternal;
                            $dataPeserta->penguji_external = $pengujiExternal;
                            $dataPeserta->saveOrFail();
                        }
                    }
                }
            }
            return $response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    private function generateNopes(Ujian $ujian){
        try {
            $number = Peserta::where('ujian', $ujian->id)->orderBy('nopes','desc')->limit(1)->get('nopes');
            if ($number->count() == 0) {
                $number = 1;
            } else {
                $number = $number->first()->nopes;
                $number = substr($number,-4);
                $number = (int) $number;
                $number = $number + 1;
            }
            $string = explode('-',$ujian->id);
            $string = $string[1];
            $string = strtoupper($string);
            return $string . str_pad($number,4,'0',STR_PAD_LEFT);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function create(Request $request) {
        try {
            $ujian = Ujian::where('id', $request->jadwal)->first();
            if (collect($request->peserta)->count() > 0) {
                foreach ($request->peserta as $inputPeserta){
                    if (strlen($inputPeserta['value']) > 0) {
                        $peserta = Peserta::where('id', $inputPeserta['value'])->first();
                    } else {
                        $peserta = new Peserta();
                        $peserta->id = Uuid::uuid4()->toString();
                    }
                    $peserta->nopes = strlen($inputPeserta['nopes']) === 0 ? $this->generateNopes($ujian) : $inputPeserta['nopes'];
                    $peserta->user = $inputPeserta['siswa'];
                    $peserta->paket = $inputPeserta['paket_soal'];
                    $peserta->ujian = $ujian->id;
                    $peserta->penguji_internal = $inputPeserta['penguji_internal'];
                    $peserta->penguji_external = $inputPeserta['penguji_external'];
                    $peserta->saveOrFail();
                }
            }
            if (collect($request->deleted_peserta)->count() > 0) {
                foreach ($request->deleted_peserta as $inputPeserta){
                    Peserta::where('id', $inputPeserta)->delete();
                }
            }
            return $request->all();
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    private function pesertaKeterampilan(Peserta $peserta){
        try {
            $response = collect([]);
            /*$persiapan = KeterampilanKomponen::where('komponen','persiapan')->where('paket', $peserta->paket)->orderBy('nomor','asc')->get();
            foreach ($persiapan as $item) {
                $data = [
                    'value' => $item->value,
                    'label' => $item->name,
                    'meta' => [
                        'komponen' => $item->komponen,
                        'penguji' => $item->penguji_type,
                        'nomor' => $item->nomor,
                        'nilai' => [
                            'sb' => $item->nilai_sangat_baik,
                            'b' => $item->nilai_baik,
                            'c' => $item->nilai_cukup,
                            't' => $item->nilai_tidak
                        ],
                        'indikator' => collect([])
                    ]
                ];
                dd($data);
                $indikators = KeterampilanIndikator::where('komponen', $item->id)->orderBy('nomor','asc')->get();
                foreach ($indikators as $indikator){
                    $item->indikator->push([
                        'value' => $indikator->id,
                        'label' => $indikator->indikator,
                    ]);
                }
                $response->push($data);
            }
            dd($response);*/
            $persiapan = $this->komponenKeterampilanRepository->table(new Request(['paket' => $peserta->paket,'komponen' => 'persiapan']));
            foreach ($persiapan as $item){
                foreach ($item['meta']['indikator'] as $index => $indikator){
                    $item['meta']['indikator'][$index]->komponen = $indikator->komponenObj;
                    $item['meta']['indikator'][$index]->capaian = CapaianKeterampilan::where('indikator', $indikator->id)
                        ->where('peserta', $peserta->id)->where('ujian', $peserta->ujian)->first();
                }
                $response->push($item);
            }
            $pelaksanaan = $this->komponenKeterampilanRepository->table(new Request(['paket' => $peserta->paket,'komponen' => 'pelaksanaan']));
            foreach ($pelaksanaan as $item){
                foreach ($item['meta']['indikator'] as $index => $indikator){
                    $item['meta']['indikator'][$index]->komponen = $indikator->komponenObj;
                    $item['meta']['indikator'][$index]->capaian = CapaianKeterampilan::where('indikator', $indikator->id)
                        ->where('peserta', $peserta->id)->where('ujian', $peserta->ujian)->first();
                }
                $response->push($item);
            }
            $hasil = $this->komponenKeterampilanRepository->table(new Request(['paket' => $peserta->paket,'komponen' => 'hasil']));
            foreach ($hasil as $item){
                foreach ($item['meta']['indikator'] as $index => $indikator){
                    $item['meta']['indikator'][$index]->komponen = $indikator->komponenObj;
                    $item['meta']['indikator'][$index]->capaian = CapaianKeterampilan::where('indikator', $indikator->id)
                        ->where('peserta', $peserta->id)->where('ujian', $peserta->ujian)->first();
                }
                $response->push($item);
            }
            return $response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    private function komponenKeterampilan(Peserta $peserta, $penguji){
        try {
            $response = collect([]);
            $komponens = KeterampilanKomponen::where('komponen','persiapan')->where('penguji_type', $penguji)->where('paket', $peserta->paket)->orderBy('nomor','asc')->get();
            foreach ($komponens as $komponen){
                $komponenIndikator = collect([]);
                $indikators = KeterampilanIndikator::where('komponen', $komponen->id)->orderBy('nomor','asc')->get();
                foreach ($indikators as $indikator){
                    $capaianIndikator = null;
                    $capaian = CapaianKeterampilan::where('indikator', $indikator->id)->where('peserta', $peserta->id)->where('ujian', $peserta->ujian)->get();
                    if ($capaian->count() > 0) {
                        $capaianIndikator = [
                            'value' => $capaian->first()->id,
                            'label' => $capaian->first()->nilai,
                        ];
                    }
                    $komponenIndikator->push([
                        'value' => $indikator->id,
                        'label' => $indikator->indikator,
                        'meta' => [
                            'nomor_komponen' => $komponen->nomor,
                            'komponen' => $indikator->komponen,
                            'nomor' => $indikator->nomor,
                            'capaian' => $capaianIndikator
                        ]
                    ]);
                }
                $response->push([
                    'value' => $komponen->id,
                    'label' => $komponen->name,
                    'meta' => [
                        'komponen' => $komponen->komponen,
                        'nomor' => $komponen->nomor,
                        'nilai' => [
                            'sb' => $komponen->nilai_sangat_baik,
                            'b' => $komponen->nilai_baik,
                            'c' => $komponen->nilai_cukup,
                            't' => $komponen->nilai_tidak
                        ],
                        'indikator' => $komponenIndikator
                    ]
                ]);
            }
            $komponens = KeterampilanKomponen::where('komponen','pelaksanaan')->where('penguji_type', $penguji)->where('paket', $peserta->paket)->orderBy('nomor','asc')->get();
            foreach ($komponens as $komponen){
                $komponenIndikator = collect([]);
                $indikators = KeterampilanIndikator::where('komponen', $komponen->id)->orderBy('nomor','asc')->get();
                foreach ($indikators as $indikator){
                    $capaianIndikator = null;
                    $capaian = CapaianKeterampilan::where('indikator', $indikator->id)->where('peserta', $peserta->id)->where('ujian', $peserta->ujian)->get();
                    if ($capaian->count() > 0) {
                        $capaianIndikator = [
                            'value' => $capaian->first()->id,
                            'label' => $capaian->first()->nilai,
                        ];
                    }
                    $komponenIndikator->push([
                        'value' => $indikator->id,
                        'label' => $indikator->indikator,
                        'meta' => [
                            'nomor_komponen' => $komponen->nomor,
                            'komponen' => $indikator->komponen,
                            'nomor' => $indikator->nomor,
                            'capaian' => $capaianIndikator
                        ]
                    ]);
                }
                $response->push([
                    'value' => $komponen->id,
                    'label' => $komponen->name,
                    'meta' => [
                        'komponen' => $komponen->komponen,
                        'nomor' => $komponen->nomor,
                        'nilai' => [
                            'sb' => $komponen->nilai_sangat_baik,
                            'b' => $komponen->nilai_baik,
                            'c' => $komponen->nilai_cukup,
                            't' => $komponen->nilai_tidak
                        ],
                        'indikator' => $komponenIndikator
                    ]
                ]);
            }
            $komponens = KeterampilanKomponen::where('komponen','hasil')->where('penguji_type', $penguji)->where('paket', $peserta->paket)->orderBy('nomor','asc')->get();
            foreach ($komponens as $komponen){
                $komponenIndikator = collect([]);
                $indikators = KeterampilanIndikator::where('komponen', $komponen->id)->orderBy('nomor','asc')->get();
                foreach ($indikators as $indikator){
                    $capaianIndikator = null;
                    $capaian = CapaianKeterampilan::where('indikator', $indikator->id)->where('peserta', $peserta->id)->where('ujian', $peserta->ujian)->get();
                    if ($capaian->count() > 0) {
                        $capaianIndikator = [
                            'value' => $capaian->first()->id,
                            'label' => $capaian->first()->nilai,
                        ];
                    }
                    $komponenIndikator->push([
                        'value' => $indikator->id,
                        'label' => $indikator->indikator,
                        'meta' => [
                            'nomor_komponen' => $komponen->nomor,
                            'komponen' => $indikator->komponen,
                            'nomor' => $indikator->nomor,
                            'capaian' => $capaianIndikator
                        ]
                    ]);
                }
                $response->push([
                    'value' => $komponen->id,
                    'label' => $komponen->name,
                    'meta' => [
                        'komponen' => $komponen->komponen,
                        'nomor' => $komponen->nomor,
                        'nilai' => [
                            'sb' => $komponen->nilai_sangat_baik,
                            'b' => $komponen->nilai_baik,
                            'c' => $komponen->nilai_cukup,
                            't' => $komponen->nilai_tidak
                        ],
                        'indikator' => $komponenIndikator
                    ]
                ]);
            }
            return $response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function table(Request $request) {
        try {
            $user_agent = $request->header('user-agent');
            $user = auth()->guard('api')->user();

            $response = collect([]);
            $pesertas = Peserta::orderBy('nopes','asc');
            if (strlen($request->user) > 0) $pesertas = $pesertas->where('user', $request->user);
            if (strlen($request->id) > 0) $pesertas = $pesertas->where('id', $request->id);
            if (strlen($request->ujian) > 0) $pesertas = $pesertas->where('ujian', $request->ujian);
            if ($user_agent == 'android'){
                switch ($user->user_type){
                    case 'siswa' :
                        $pesertas = $pesertas->where('user', $user->id);
                        break;
                    case 'guru' :
                        $kolom_penguji = $user->penguji_type == 'internal' ? 'penguji_internal' : 'penguji_external';
                        $pesertas = $pesertas->where($kolom_penguji, $user->id);
                        break;
                    case 'admin' :
                        $pesertas = $pesertas->where('penguji_internal','asd');
                        break;
                }
            }

            $pesertas = $pesertas->get();
            foreach ($pesertas as $peserta){
                $user = $this->userRepository->table(new Request(['id' => $peserta->user]))->first();
                $internal = $this->userRepository->table(new Request(['id' => $peserta->penguji_internal]))->first();
                $internal['meta']['komponen'] = collect([]);
                $external = $this->userRepository->table(new Request(['id' => $peserta->penguji_external]))->first();
                $external['meta']['komponen'] = collect([]);
                if ($user_agent == 'android'){
                    $internal['meta']['komponen'] = $this->komponenKeterampilan($peserta,'internal');
                    $external['meta']['komponen'] = $this->komponenKeterampilan($peserta,'external');
                }
                if ($request->minimal) {
                    $paket = collect([]);
                } else {
                    $paket = $this->packageRepository->table(new Request(['id' => $peserta->paket, 'no_komponen' => 1]))->first();
                }
                $response->push([
                    'value' => $peserta->id,
                    'label' => $user['label'],
                    'meta' => [
                        'nopes' => $peserta->nopes,
                        'user' => $user,
                        'penguji' => [
                            'internal' => $internal,
                            'external' => $external,
                        ],
                        'paket' => $paket
                    ]
                ]);
            }
            return $response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
}