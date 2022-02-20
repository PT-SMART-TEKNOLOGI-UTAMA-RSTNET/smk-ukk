<?php
/**
 * Created by PhpStorm.
 * User: reyang
 * Date: 2/20/2022
 * Time: 12:37 PM
 */

namespace App\Validations;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AsessorValidation
{
    public function create(Request $request) {
        try {
            $valid = Validator::make($request->all(),[
                'nama_asessor' => 'required|string|min:2',
                'jenis_asessor' => 'required|array|size:2',
                'jenis_asessor.value' => 'required|string|in:internal,external',
                'email_asessor' => 'required|email|unique:users,email',
                'kata_sandi' => 'required|string|min:6|confirmed'
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
                'id' => 'required|string|min:10|exists:users,id',
                'nama_asessor' => 'required|string|min:2',
                'jenis_asessor' => 'required|array|size:2',
                'jenis_asessor.value' => 'required|string|in:internal,external',
                'email_asessor' => 'required|email|unique:users,email,' . $request->id . ',id',
                'kata_sandi' => 'nullable|confirmed'
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
                'id' => 'required|string|min:10|exists:users,id',
            ]);
            if ($valid->fails()) throw new \Exception(collect($valid->errors()->all())->join("<br>"),400);
            return $request;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),400);
        }
    }
}