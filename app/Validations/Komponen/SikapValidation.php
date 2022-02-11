<?php
/**
 * Created by PhpStorm.
 * User: reyang
 * Date: 2/11/2022
 * Time: 11:39 AM
 */

namespace App\Validations\Komponen;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SikapValidation
{
    public function delete(Request $request) {
        try {
            $valid = Validator::make($request->all(),[
                'id' => 'required|string|min:10|exists:sikap_indikators,id',
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
                'id' => 'required|string|min:10|exists:sikap_indikators,id',
                'paket' => 'required|string|min:10|exists:paket_soals,id',
                'indikator_sikap' => 'required|string|min:1'
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
                'paket' => 'required|string|min:10|exists:paket_soals,id',
                'indikator_sikap' => 'required|string|min:1'
            ]);
            if ($valid->fails()) throw new \Exception(collect($valid->errors()->all())->join("<br>"),400);
            return $request;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),400);
        }
    }
}