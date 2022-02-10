<?php
/**
 * Created by PhpStorm.
 * User: reyang
 * Date: 2/10/2022
 * Time: 10:18 AM
 */

namespace App\Validations;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PackageValidation
{
    public function create(Request $request) {
        try {
            $valid = Validator::make($request->all(),[
                'jurusan' => 'required|string|exists:' . config('database.erapor') . '.jurusans,id',
                'judul_paket_soal' => 'required|string|min:2',
                'judul_paket_soal_inggris' => 'required|string|min:2',
                'keterangan' => 'required|string|min:10',
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
                'id' => 'required|string|min:10|exists:paket_soals,id',
                'jurusan' => 'required|string|exists:' . config('database.erapor') . '.jurusans,id',
                'judul_paket_soal' => 'required|string|min:2',
                'judul_paket_soal_inggris' => 'required|string|min:2',
                'keterangan' => 'required|string|min:10',
            ]);
            if ($valid->fails()) throw new \Exception(collect($valid->errors()->all())->join("<br>"),400);
            return $request;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),400);
        }
    }
    public function delete(Request $request) {
        try {
            $valid = Validator::make($request->all(),[
                'id' => 'required|string|exists:paket_soals,id',
            ]);
            if ($valid->fails()) throw new \Exception(collect($valid->errors()->all())->join("<br>"),400);
            return $request;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),400);
        }
    }
}