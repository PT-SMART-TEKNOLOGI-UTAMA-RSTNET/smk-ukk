<?php
/**
 * Created by PhpStorm.
 * User: reyang
 * Date: 1/11/2022
 * Time: 10:54 AM
 */

namespace App\Repositories;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravolt\Avatar\Avatar;

class AuthRepository
{
    public function login(Request $request){
        try {
            $user = User::where('email', $request->nama_pengguna)->first();
            if (! Hash::check($request->kata_sandi, $user->password)) throw new \Exception('Kombinasi nama pengguna dan kata sandi tidak valid',400);
            auth()->login($user);
            $token = $user->createToken('erapor')->accessToken;
            return $token;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function currentUser(Request $request){
        try {
            $user = auth()->guard('api')->user();
            $response = [
                'value' => $user->id,
                'label' => $user->name,
                'meta' => [
                    'avatar' => (new \Laravolt\Avatar\Avatar)->create($user->name)->setTheme('colorful')->toBase64(),
                    'email' => $user->email,
                    'level' => $user->user_type,
                    'penguji' => [
                        'type' => $user->penguji_type,
                        'internal' => $user->penguji_internal,
                        'external' => $user->penguji_external
                    ],
                    'kode' => [
                        'nis' => $user->nis,
                        'nisn' => $user->nisn,
                        'nopes' => $user->nopes
                    ]
                ]
            ];
            return $response;
        } catch (\Exception $exception){
            throw new \Exception($exception->getMessage(),500);
        }
    }
}