<?php
/**
 * Created by PhpStorm.
 * User: reyang
 * Date: 2/11/2022
 * Time: 8:43 PM
 */

namespace App\Validations;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PesertaValidation
{
    public function importPeserta(Request $request) {
        try {
            $valid = Validator::make($request->all(),[
                'ujian' => 'required|string|min:10|exists:ujians,id',
                'file' => 'required|file|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]);
            if ($valid->fails()) throw new \Exception(collect($valid->errors()->all())->join("<br>"),400);
            return $request;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),400);
        }
    }
    public function create(Request $request){
        try {
            //dd($request->all());
            $valid = Validator::make($request->all(),[
                'jadwal' => 'required|string|min:10|exists:ujians,id',
                'peserta' => 'nullable|array',
                'peserta.*.value' => 'nullable|exists:pesertas,id',
                'peserta.*.siswa' => 'required|string|min:10|exists:users,id',
                'peserta.*.nopes' => 'nullable',
                'peserta.*.paket_soal' => 'required|string|min:10|exists:paket_soals,id',
                'peserta.*.penguji_internal' => 'required|string|min:10|exists:users,id',
                'peserta.*.penguji_external' => 'required|string|min:10|exists:users,id',
                'deleted_peserta' => 'nullable|array',
                'deleted_peserta.*' => 'required|string|min:10|exists:pesertas,id'
            ]);
            if ($valid->fails()) throw new \Exception(collect($valid->errors()->all())->join("<br>"),400);
            return $request;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),400);
        }
    }
}