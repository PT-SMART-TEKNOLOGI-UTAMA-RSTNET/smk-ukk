<?php

namespace App\Http\Controllers;

use App\Repositories\PesertaRepository;
use App\Validations\PesertaValidation;
use Illuminate\Http\Request;

class PesertaController extends Controller
{
    protected $repository;
    protected $validation;
    public function __construct()
    {
        $this->repository = new PesertaRepository();
        $this->validation = new PesertaValidation();
    }
    public function crud(Request $request){
        try {
            $code = 400; $message = 'Undefined Method'; $params = null;
            switch (strtolower($request->method())){
                case 'post' :
                    $params = $this->repository->table($request);
                    $code = 200; $message = 'ok';
                    break;
                case 'put' :
                    $valid = $this->validation->create($request);
                    $params = $this->repository->create($valid);
                    $code = 200; $message = 'Peserta ujian berhasil disimpan';
                    break;
            }
            return responseFormat($code, $message, $params);
        } catch (\Exception $exception) {
            return responseFormat($exception->getCode(), $exception->getMessage());
        }
    }
}
