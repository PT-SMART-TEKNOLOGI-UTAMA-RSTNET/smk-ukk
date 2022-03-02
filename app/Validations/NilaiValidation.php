<?php
/**
 * Created by PhpStorm.
 * User: reyang
 * Date: 2/11/2022
 * Time: 10:57 PM
 */

namespace App\Validations;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NilaiValidation
{
    public function pengetahuanSave(Request $request){
        try {
            $valid = Validator::make($request->all(),[
                'peserta' => 'required|string|min:10|exists:pesertas,id',
                'soal' => 'required|string|min:10|exists:pengetahuan_komponens,id',
                'nilai' => 'required|numeric|min:0|max:100'
            ]);
            if ($valid->fails()) throw new \Exception(collect($valid->errors()->all())->join("<br>"),400);
            return $request;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),400);
        }
    }
    public function downloadNilai(Request $request){
        try {
            $request = $request->merge(['ujian' => $request->segment(3)]);
            $valid = Validator::make($request->all(),[
                'ujian' => 'required|string|min:10|exists:ujians,id'
            ]);
            if ($valid->fails()) throw new \Exception(collect($valid->errors()->all())->join("<br>"),400);
            return $request;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function cetakKartuUjian(Request $request){
        try {
            $request = $request->merge(['peserta' => $request->segment(3)]);
            $valid = Validator::make($request->all(),[
                'ujian' => 'required|string|min:10|exists:ujians,id'
            ]);
            if ($valid->fails()) throw new \Exception(collect($valid->errors()->all())->join("<br>"),400);
            return $request;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),400);
        }
    }
    public function cetakLembarNilai(Request $request){
        try {
            $request = $request->merge(['peserta' => $request->segment(3)]);
            $valid = Validator::make($request->all(),[
                'peserta' => 'required|string|min:10|exists:pesertas,id'
            ]);
            if ($valid->fails()) throw new \Exception(collect($valid->errors()->all())->join("<br>"),400);
            return $request;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),400);
        }
    }
    public function create(Request $request) {
        try {
            $valid = Validator::make($request->all(),[
                'nilai' => 'required|string|in:yes,no',
                'indikator' => 'required|string|min:10|exists:keterampilan_indikators,id',
                'peserta' => 'required|string|min:10|exists:pesertas,id',
                'ujian' => 'required|string|min:10|exists:ujians,id'
            ]);
            if ($valid->fails()) throw new \Exception(collect($valid->errors()->all())->join("<br>"),400);
            return $request;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
}