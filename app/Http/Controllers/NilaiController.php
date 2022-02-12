<?php

namespace App\Http\Controllers;

use App\Repositories\NilaiRepository;
use App\Validations\NilaiValidation;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    protected $repository;
    protected $validation;
    public function __construct()
    {
        $this->repository = new NilaiRepository();
        $this->validation = new NilaiValidation();
    }
    public function crud(Request $request) {
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
                    $code = 200; $message = 'Nilai berhasil dibuat';
                    break;
            }
            return responseFormat($code, $message, $params);
        } catch (\Exception $exception) {
            return responseFormat($exception->getCode(), $exception->getMessage());
        }
    }
}
