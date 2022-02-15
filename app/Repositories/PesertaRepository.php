<?php
/**
 * Created by PhpStorm.
 * User: reyang
 * Date: 2/11/2022
 * Time: 2:53 PM
 */

namespace App\Repositories;


use App\Models\CapaianKeterampilan;
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
    public function __construct()
    {
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
            foreach ($request->peserta as $inputPeserta){
                if (strlen($inputPeserta['value']) > 0) {
                    $peserta = Peserta::where('id', $inputPeserta['value'])->first();
                } else {
                    $peserta = new Peserta();
                    $peserta->id = Uuid::uuid4()->toString();
                    $peserta->nopes = $this->generateNopes($ujian);
                }
                $peserta->user = $inputPeserta['siswa']['value'];
                $peserta->paket = $inputPeserta['paket_soal']['value'];
                $peserta->ujian = $ujian->id;
                $peserta->penguji_internal = $inputPeserta['penguji_internal']['value'];
                $peserta->penguji_external = $inputPeserta['penguji_external']['value'];
                $peserta->saveOrFail();
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
    public function table(Request $request) {
        try {
            $response = collect([]);
            $pesertas = Peserta::orderBy('nopes','asc');
            if (strlen($request->id) > 0) $pesertas = $pesertas->where('id', $request->id);
            if (strlen($request->ujian) > 0) $pesertas = $pesertas->where('ujian', $request->ujian);
            $pesertas = $pesertas->get();
            foreach ($pesertas as $peserta){
                $user = $peserta->userObj;
                $paket = $peserta->paketObj;
                $paket->komponen = new \StdClass();
                $paket->komponen->keterampilan = $this->pesertaKeterampilan($peserta);
                $response->push([
                    'value' => $peserta->id,
                    'label' => $user->name,
                    'meta' => [
                        'tingkat' => $user->tingkat,
                        'user' => $user,
                        'nis' => $user->nis,
                        'jurusan' => $user->jurusanObj,
                        'rombel' => $user->rombel,
                        'nopes' => $peserta->nopes,
                        'penguji' => [
                            'internal' => $peserta->internalObj,
                            'external' => $peserta->externalObj
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