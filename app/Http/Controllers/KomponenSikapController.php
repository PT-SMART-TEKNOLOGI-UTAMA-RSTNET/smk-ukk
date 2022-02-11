<?php

namespace App\Http\Controllers;

use App\Repositories\Komponen\SikapRepository;
use App\Validations\Komponen\SikapValidation;
use Illuminate\Http\Request;

class KomponenSikapController extends Controller
{
    protected $validation;
    protected $repository;
    public function __construct()
    {
        $this->validation = new SikapValidation();
        $this->repository = new SikapRepository();
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
                    $code = 200; $message = 'Komponen sikap berhasil dibuat';
                    break;
                case 'patch' :
                    $valid = $this->validation->update($request);
                    $params = $this->repository->update($valid);
                    $code = 200; $message = 'Komponen sikap berhasil diubah';
                    break;
                case 'delete' :
                    $valid = $this->validation->delete($request);
                    $params = $this->repository->delete($valid);
                    $code = 200; $message = 'Komponen sikap berhasil dihapus';
                    break;
            }
            return responseFormat($code, $message, $params);
        } catch (\Exception $exception) {
            return responseFormat($exception->getCode(), $exception->getMessage());
        }
    }
}
