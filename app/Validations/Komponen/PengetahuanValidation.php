<?php
/**
 * Created by PhpStorm.
 * User: reyang
 * Date: 2/14/2022
 * Time: 9:27 AM
 */

namespace App\Validations\Komponen;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PengetahuanValidation
{
    public function jawab(Request $request){
        try {
            $valid = Validator::make($request->all(),[
                'ujian' => 'required|string|min:10|exists:ujians,id',
                'peserta' => 'required|string|min:10|exists:pesertas,id',
                'komponen' => 'required|string|min:10|exists:pengetahuan_komponens,id',
                'indikator' => 'required|string|min:10|exists:pengetahuan_indikators,id',
                'isi_jawaban' => 'nullable'
            ]);
            if ($valid->fails()) throw new \Exception(collect($valid->errors()->all())->join("<br>"),400);
            return $request;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),400);
        }
    }
    public function delete(Request $request){
        try {
            $valid = Validator::make($request->all(),[
                'id' => 'required|string|min:10|exists:pengetahuan_komponens,id'
            ]);
            if ($valid->fails()) throw new \Exception(collect($valid->errors()->all())->join("<br>"),400);
            return $request;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),400);
        }
    }
    public function update(Request $request) {
        try {
            $valid = Validator::make($request->all(),[
                'id' => 'required|min:10|exists:pengetahuan_komponens,id',
                'paket' => 'required|min:10|exists:paket_soals,id',
                'isi_soal' => 'required|string|min:10',
                'jenis_soal' => 'required|array|size:2',
                'jenis_soal.value' => 'required|string|in:pg,essay',
                'jawaban' => 'required|array|size:4',
                'jawaban.nomor' => 'required|numeric|min:1',
                'pilihan_jawaban' => 'required|array|min:1',
                'pilihan_jawaban.*.label' => 'required|string|min:1',
                'pilihan_jawaban.*.value' => 'nullable|exists:pengetahuan_indikators,id',
                'pilihan_jawaban.*.nomor' => 'required|numeric|min:1',
                'deleted_jawaban' => 'nullable|array',
                'deleted_jawaban.*.value' => 'required|string|min:10|exists:pengetahuan_indikators,id'
            ]);
            if ($valid->fails()) throw new \Exception(collect($valid->errors()->all())->join("<br>"),400);
            //if ($request->jenis_soal['value'] == 'essay' && collect($request->pilihan_jawaban)->count() > 1) throw new \Exception('Pilihan jawaban untuk Jenis soal isian tidak boleh lebih dari 1 (satu)',400);
            return $request;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),400);
        }
    }
    public function create(Request $request) {
        try {
            $valid = Validator::make($request->all(),[
                'paket' => 'required|min:10|exists:paket_soals,id',
                'isi_soal' => 'required|string|min:10',
                'jenis_soal' => 'required|array|size:2',
                'jenis_soal.value' => 'required|string|in:pg,essay',
                'jawaban' => 'required|array|size:4',
                'jawaban.nomor' => 'required|numeric|min:1',
                'pilihan_jawaban' => 'required|array|min:1',
                'pilihan_jawaban.*.label' => 'required|string|min:1',
                'pilihan_jawaban.*.nomor' => 'required|numeric|min:1'
            ]);
            if ($valid->fails()) throw new \Exception(collect($valid->errors()->all())->join("<br>"),400);
            //if ($request->jenis_soal['value'] == 'essay' && collect($request->pilihan_jawaban)->count() > 1) throw new \Exception('Pilihan jawaban untuk Jenis soal isian tidak boleh lebih dari 1 (satu)',400);
            return $request;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),400);
        }
    }
}