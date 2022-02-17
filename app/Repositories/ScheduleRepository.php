<?php
/**
 * Created by PhpStorm.
 * User: reyang
 * Date: 2/11/2022
 * Time: 12:23 PM
 */

namespace App\Repositories;


use App\Models\Peserta;
use App\Models\Ujian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class ScheduleRepository
{
    protected $pesertaRepository;
    protected $jurusanRepository;
    public function __construct()
    {
        $this->jurusanRepository = new JurusanRepository();
        $this->pesertaRepository = new PesertaRepository();
    }

    public function delete(Request $request) {
        try {
            Ujian::where('id', $request->id)->delete();
            return $request->all();
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function update(Request $request){
        try {
            $ujians = Ujian::where('id', $request->id)->first();
            $ujians->jurusan = $request->jurusan;
            $ujians->name = $request->judul_ujian;
            $ujians->start_at = $request->tanggal_mulai;
            $ujians->end_at = $request->tanggal_selesai;
            $ujians->description = $request->keterangan_ujian;
            $ujians->tingkat = $request->tingkat;
            $ujians->saveOrFail();
            return $this->table(new Request(['id' => $ujians->id]));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function create(Request $request){
        try {
            $ujians = new Ujian();
            $ujians->id = Uuid::uuid4()->toString();
            $ujians->jurusan = $request->jurusan;
            $ujians->name = $request->judul_ujian;
            $ujians->start_at = $request->tanggal_mulai;
            $ujians->end_at = $request->tanggal_selesai;
            $ujians->description = $request->keterangan_ujian;
            $ujians->token = strtoupper(Str::random(6));
            $ujians->tingkat = $request->tingkat;
            $ujians->saveOrFail();
            return $this->table(new Request(['id' => $ujians->id]));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function table(Request $request) {
        try {
            $user_agent = $request->header('user-agent');
            $user = auth()->guard('api')->user();
            $response = collect([]);
            $is_penguji = false;
            $dateNow = Carbon::now()->format('Y-m-d H:i:s');
            $ujians = Ujian::orderBy('start_at','desc');
            if (strlen($request->id) > 0) $ujians = $ujians->where('id', $request->id);
            switch ($user->user_type){
                case 'siswa' :
                    $distinctUjianId = Peserta::where('user', $user->id)->select('ujian')->groupBy('ujian')->get()->map(function ($data){ return $data->ujian; });
                    $ujians = $ujians->whereIn('id', $distinctUjianId)->where('start_at', '<=', $dateNow)->where('end_at', '>=', $dateNow);
                    break;
                case 'guru' :
                    $kolom_penguji = $user->penguji_type == 'internal' ? 'penguji_internal' : 'penguji_external';
                    $distinctUjianId = Peserta::where($kolom_penguji, $user->id)->select('ujian')->groupBy('ujian')->get()->map(function ($data){ return $data->ujian; });
                    $ujians = $ujians->whereIn('id', $distinctUjianId)->where('start_at', '<=', $dateNow)->where('end_at', '>=', $dateNow);
                    $is_penguji = true;
                    break;
            }
            $ujians = $ujians->get();
            foreach ($ujians as $ujian){
                if ($user_agent == 'android'){
                    $peserta = collect([]);
                    if ($user->user_type == 'siswa') {
                        $peserta = $this->pesertaRepository->table(new Request(['ujian' => $ujian->id, 'user' => $user->id]));
                    }
                } else {
                    $peserta = $this->pesertaRepository->table(new Request(['ujian' => $ujian->id]));
                }
                $response->push([
                    'value' => $ujian->id,
                    'label' => $ujian->name,
                    'meta' => [
                        'tingkat' => $ujian->tingkat,
                        'keterangan' => $ujian->description,
                        'token' => [
                            'string' => $ujian->token,
                            'expired' => $ujian->token_expired_at
                        ],
                        'jurusan' => $this->jurusanRepository->table(new Request(['id' => $ujian->jurusan]))->first(),
                        'tanggal' => [
                            'mulai' => $ujian->start_at,
                            'selesai' => $ujian->end_at
                        ],
                        'active' => $ujian->is_active,
                        'peserta' => $peserta,
                        'is_penguji' => $is_penguji
                    ]
                ]);
            }
            return $response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
}