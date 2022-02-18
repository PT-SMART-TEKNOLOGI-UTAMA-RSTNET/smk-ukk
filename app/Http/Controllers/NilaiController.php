<?php

namespace App\Http\Controllers;

use App\Repositories\NilaiRepository;
use App\Repositories\PackageRepository;
use App\Repositories\PesertaRepository;
use App\Repositories\ScheduleRepository;
use App\Validations\NilaiValidation;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    protected $repository;
    protected $validation;
    protected $pesertaRepository;
    protected $packageRepository;
    protected $scheduleRepository;
    public function __construct()
    {
        $this->scheduleRepository = new ScheduleRepository();
        $this->packageRepository = new PackageRepository();
        $this->pesertaRepository = new PesertaRepository();
        $this->repository = new NilaiRepository();
        $this->validation = new NilaiValidation();
    }
    public function downloadNilai(Request $request){
        try {
            $valid = $this->validation->downloadNilai($request);
            $this->repository->downloadNilai($valid);
        } catch (\Exception $exception) {
            return responseFormat($exception->getCode(), $exception->getMessage());
        }
    }
    public function cetakLembarNilai(Request $request) {
        try {
            $valid = $this->validation->cetakLembarNilai($request);
            $peserta = $this->repository->table($valid)->first();
            $peserta['meta']['paket'] = $this->packageRepository->table(new Request(['id' => $peserta['meta']['paket']]))->first();
            $peserta['meta']['ujian'] = $this->scheduleRepository->minTable(new Request(['id' => $peserta['meta']['ujian']]))->first();
            return view('cetakLembarNilai', compact('peserta'));
        } catch (\Exception $exception) {
            return responseFormat($exception->getCode(), $exception->getMessage());
        }
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
