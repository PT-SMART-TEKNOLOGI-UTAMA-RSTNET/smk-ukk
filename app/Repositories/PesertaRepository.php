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
use App\Models\Peserta;
use App\Models\Ujian;
use App\Repositories\Komponen\KeterampilanRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

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
                    $peserta->user = $inputPeserta['siswa']['value'];
                    $peserta->paket = $inputPeserta['paket_soal']['value'];
                    $peserta->ujian = $ujian->id;
                    $peserta->penguji_internal = $inputPeserta['penguji_internal']['value'];
                    $peserta->penguji_external = $inputPeserta['penguji_external']['value'];
                    $peserta->saveOrFail();
                }
            }
            if (collect($request->deleted_peserta)->count() > 0) {
                foreach ($request->deleted_peserta as $inputPeserta){
                    Peserta::where('id', $inputPeserta['value'])->delete();
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
                        'paket' => $this->packageRepository->table(new Request(['id' => $peserta->paket, 'no_komponen' => 1]))->first()
                    ]
                ]);
            }
            return $response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
}