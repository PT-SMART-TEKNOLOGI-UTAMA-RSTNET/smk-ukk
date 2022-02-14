<?php

namespace App\Http\Controllers;

use App\Repositories\Komponen\PengetahuanRepository;
use App\Validations\Komponen\PengetahuanValidation;
use Illuminate\Http\Request;

class KomponenPengetahuanController extends Controller
{
    protected $validation;
    protected $repository;
    public function __construct()
    {
        $this->repository = new PengetahuanRepository();
        $this->validation = new PengetahuanValidation();
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
                    $code = 200; $message = 'Soal berhasil dibuat';
                    break;
                case 'patch' :
                    $valid = $this->validation->update($request);
                    $params = $this->repository->update($valid);
                    $code = 200; $message = 'Soal berhasil diubah';
                    break;
                case 'delete' :
                    $valid = $this->validation->delete($request);
                    $params = $this->repository->delete($valid);
                    $code = 200; $message = 'Soal berhasil dihapus';
                    break;
            }
            return responseFormat($code, $message, $params);
        } catch (\Exception $exception) {
            return responseFormat($exception->getCode(), $exception->getMessage());
        }
    }
    public function soal(Request $request) {
        try {
            $code = 400; $message = 'Undefined Method'; $params = null;
            switch (strtolower($request->method())){
                case 'post' :
                    $params = $this->repository->table($request);
                    $code = 200; $message = 'ok';
                    break;
                case 'put' :
                    $valid = $this->validation->jawab($request);
                    $params = $this->repository->jawab($valid);
                    $code = 200; $message = 'ok';
                    break;
            }
            return responseFormat($code, $message, $params);
        } catch (\Exception $exception) {
            return responseFormat($exception->getCode(), $exception->getMessage());
        }
    }
}
