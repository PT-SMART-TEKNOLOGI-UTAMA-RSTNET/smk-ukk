<?php
/**
 * Created by PhpStorm.
 * User: reyang
 * Date: 1/11/2022
 * Time: 10:53 AM
 */

namespace App\Validations;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthValidation
{
    /* @
     * @param Request $request
     * @return Request
     * @throws \Exception
     */
    public function login(Request $request){
        try {
            $valid = Validator::make($request->all(),[
                'nama_pengguna' => 'required|string|exists:users,email',
                'kata_sandi' => 'required|string|min:6'
            ]);
            if ($valid->fails()) throw new \Exception(collect($valid->errors()->all())->join("\n"),400);
            return $request;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),400);
        }
    }
}