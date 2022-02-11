<?php
/**
 * Created by PhpStorm.
 * User: reyang
 * Date: 2/11/2022
 * Time: 12:23 PM
 */

namespace App\Repositories;


use App\Models\Ujian;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class ScheduleRepository
{
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
            $ujians->saveOrFail();
            return $this->table(new Request(['id' => $ujians->id]));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function table(Request $request) {
        try {
            $response = collect([]);
            $ujians = Ujian::orderBy('start_at','desc');
            if (strlen($request->id) > 0) $ujians = $ujians->where('id', $request->id);
            $ujians = $ujians->get();
            foreach ($ujians as $ujian){
                $response->push([
                    'value' => $ujian->id,
                    'label' => $ujian->name,
                    'meta' => [
                        'keterangan' => $ujian->description,
                        'token' => [
                            'string' => $ujian->token,
                            'expired' => $ujian->token_expired_at
                        ],
                        'jurusan' => $ujian->jurusanObj,
                        'tanggal' => [
                            'mulai' => $ujian->start_at,
                            'selesai' => $ujian->end_at
                        ],
                        'active' => $ujian->is_active,
                        'peserta' => $ujian->pesertaCollection,

                    ]
                ]);
            }
            return $response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
}