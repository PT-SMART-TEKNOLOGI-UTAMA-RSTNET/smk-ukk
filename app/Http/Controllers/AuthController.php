<?php

namespace App\Http\Controllers;

use App\Repositories\AuthRepository;
use App\Validations\AuthValidation;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $validation;
    protected $repository;
    public function __construct()
    {
        $this->validation = new AuthValidation();
        $this->repository = new AuthRepository();
    }

    public function users(Request $request) {
        try {
            $code = 400; $message = 'Undefined Method'; $params = null;
            switch (strtolower($request->method())){
                case 'post' :
                    $params = $this->repository->allUsers($request);
                    $code = 200; $message = 'ok';
                    break;
            }
            return responseFormat($code, $message, $params);
        } catch (\Exception $exception) {
            return responseFormat($exception->getCode(), $exception->getMessage());
        }
    }
    public function logout(Request $request){
        try {
            if (strtolower($request->method()) == 'post') {
                $params = $this->repository->logout($request);
                return responseFormat(200,'ok',$params);
            } elseif (strtolower($request->method()) == 'get') {
                if (auth()->check()){
                    auth()->logout();
                }
                return redirect(route('login'));
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function login(Request $request){
        try {
            $code = 400; $message = 'Undefined Method'; $params = null;
            switch (strtolower($request->method())) {
                case 'get' :
                    return view('auth.login');
                    break;
                case 'post' :
                    $valid = $this->validation->login($request);
                    $params = $this->repository->login($valid);
                    $code = 200; $message = 'ok';
                    break;
            }
            return responseFormat($code, $message, $params);
        } catch (\Exception $exception) {
            return responseFormat($exception->getCode(), $exception->getMessage());
        }
    }
    public function me(Request $request) {
        try {
            $params = $this->repository->currentUser($request);
            return responseFormat(200,'ok', $params);
        } catch (\Exception $exception) {
            return responseFormat($exception->getCode(), $exception->getMessage());
        }
    }
}
