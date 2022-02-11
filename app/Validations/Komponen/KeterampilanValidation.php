<?php
/**
 * Created by PhpStorm.
 * User: reyang
 * Date: 2/10/2022
 * Time: 2:04 PM
 */

namespace App\Validations\Komponen;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KeterampilanValidation
{
    public function delete(Request $request){
        try {
            $valid = Validator::make($request->all(),[
                'id' => 'required|string|min:10|exists:keterampilan_komponents,id',
            ]);
            if ($valid->fails()) throw new \Exception(collect($valid->errors()->all())->join("<br>"),400);
            return $request;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),400);
        }
    }
    public function update(Request $request){
        try {
            $valid = Validator::make($request->all(),[
                'id' => 'required|string|min:10|exists:keterampilan_komponents,id',
                'paket' => 'required|string|min:10|exists:paket_soals,id',
                'komponen.value' => 'required|string|in:persiapan,pelaksanaan,hasil',
                'sub_komponen' => 'required|string|min:10',
                'penguji.value' => 'required|string|in:internal,external',
                'nilai_baik' => 'required|numeric|min:0',
                'nilai_cukup' => 'required|numeric|min:0',
                'nilai_sangat_baik' => 'required|numeric|min:0',
                'nilai_tidak' => 'required|numeric|min:0',
                'indikator' => 'required|array|min:1',
                'indikator.*.value' => 'nullable|exists:keterampilan_indikators,id',
                'indikator.*.label' => 'required|string|min:10',
                'indikator_deleted' => 'nullable',
                'indikator_deleted.*.value' => 'required|string|min:10|exists:keterampilan_indikators,id'
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
                'komponen.value' => 'required|string|in:persiapan,pelaksanaan,hasil',
                'sub_komponen' => 'required|string|min:10',
                'penguji.value' => 'required|string|in:internal,external',
                'nilai_baik' => 'required|numeric|min:0',
                'nilai_cukup' => 'required|numeric|min:0',
                'nilai_sangat_baik' => 'required|numeric|min:0',
                'nilai_tidak' => 'required|numeric|min:0',
                'indikator' => 'required|array|min:1',
                'indikator.*.label' => 'required|string|min:10'
            ]);
            if ($valid->fails()) throw new \Exception(collect($valid->errors()->all())->join("<br>"),400);
            return $request;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),400);
        }
    }
}