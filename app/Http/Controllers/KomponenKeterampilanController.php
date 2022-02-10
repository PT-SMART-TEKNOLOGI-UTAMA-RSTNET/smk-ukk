<?php

namespace App\Http\Controllers;

use App\Repositories\Komponen\KeterampilanRepository;
use App\Validations\Komponen\KeterampilanValidation;
use Illuminate\Http\Request;

class KomponenKeterampilanController extends Controller
{
    protected $repository;
    protected $validation;
    public function __construct()
    {
        $this->repository = new KeterampilanRepository();
        $this->validation = new KeterampilanValidation();
    }
    public function crud(Request $request){
        try {
            $code = 400; $message = 'Undefined Method'; $params = null;
            switch (strtolower($request->method())){
                case 'put' :
                    $valid = $this->validation->create($request);
                    $params = $this->repository->create($valid);
                    $code = 200; $message = 'Komponen keterampilan berhasil dibuat';
                    break;
                case 'patch' :
                    $valid = $this->validation->update($request);
                    $params = $this->repository->update($valid);
                    $code = 200; $message = 'Komponen keterampilan berhasil dibuat';
                    break;
                case 'delete' :
                    $valid = $this->validation->delete($request);
                    $params = $this->repository->delete($valid);
                    $code = 200; $message = 'Komponen keterampilan berhasil dibuat';
                    break;
            }
            return responseFormat($code, $message, $params);
        } catch (\Exception $exception) {
            return responseFormat($exception->getCode(), $exception->getMessage());
        }
    }
}
