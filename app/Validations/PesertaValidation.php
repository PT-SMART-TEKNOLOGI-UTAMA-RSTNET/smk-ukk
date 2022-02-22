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
            $valid = Validator::make($request->all(),[
                'jadwal' => 'required|string|min:10|exists:ujians,id',
                'peserta' => 'nullable|array',
                'peserta.*.value' => 'nullable|exists:pesertas,id',
                'peserta.*.siswa' => 'required|array|size:3',
                'peserta.*.siswa.value' => 'required|string|min:10|exists:users,id',
                'peserta.*.siswa.nopes' => 'nullable|unique:pesertas,nopes',
                'peserta.*.paket_soal' => 'required|array|size:3',
                'peserta.*.paket_soal.value' => 'required|string|min:10|exists:paket_soals,id',
                'peserta.*.penguji_internal' => 'required|array|size:3',
                'peserta.*.penguji_internal.value' => 'required|string|min:10|exists:users,id',
                'peserta.*.penguji_external' => 'required|array|size:3',
                'peserta.*.penguji_external.value' => 'required|string|min:10|exists:users,id',
            ]);
            if ($valid->fails()) throw new \Exception(collect($valid->errors()->all())->join("<br>"),400);
            return $request;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),400);
        }
    }
}