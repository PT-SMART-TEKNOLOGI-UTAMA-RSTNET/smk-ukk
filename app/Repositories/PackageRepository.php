<?php
/**
 * Created by PhpStorm.
 * User: reyang
 * Date: 2/10/2022
 * Time: 10:18 AM
 */

namespace App\Repositories;


use App\Models\PaketSoal;
use App\Repositories\Komponen\KeterampilanRepository;
use App\Repositories\Komponen\SikapRepository;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class PackageRepository
{
    protected $keterampilanRepository;
    protected $sikapRepository;
    public function __construct()
    {
        $this->keterampilanRepository = new KeterampilanRepository();
        $this->sikapRepository = new SikapRepository();
    }

    public function delete(Request $request){
        try {
            PaketSoal::where('id', $request->id)->delete();
            return $request->all();
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function update(Request $request){
        try {
            $paketSoal = PaketSoal::where('id', $request->id)->first();
            $paketSoal->jurusan = $request->jurusan;
            $paketSoal->name = $request->judul_paket_soal;
            $paketSoal->name_eng = $request->judul_paket_soal_inggris;
            $paketSoal->description = $request->keterangan;
            $paketSoal->saveOrFail();

            return $this->table(new Request(['id' => $paketSoal->id]));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function create(Request $request) {
        try {
            $paketSoal = new PaketSoal();
            $paketSoal->id = Uuid::uuid4()->toString();
            $paketSoal->jurusan = $request->jurusan;
            $paketSoal->name = $request->judul_paket_soal;
            $paketSoal->name_eng = $request->judul_paket_soal_inggris;
            $paketSoal->description = $request->keterangan;
            $paketSoal->saveOrFail();

            return $this->table(new Request(['id' => $paketSoal->id]));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function table(Request $request) {
        try {
            $response = collect([]);
            $packages = PaketSoal::orderBy('name','desc');
            if (strlen($request->id) > 0) $packages = $packages->where('id', $request->id);
            if (strlen($request->jurusan) > 0) $packages = $packages->where('jurusan', $request->jurusan);
            $packages = $packages->get();
            foreach ($packages as $package){
                $response->push([
                    'value' => $package->id,
                    'label' => $package->name,
                    'meta' => [
                        'keterangan' => $package->description,
                        'jurusan' => $package->jurusanObj,
                        'eng' => $package->name_eng,
                        'komponen' => [
                            'keterampilan' => $this->keterampilanRepository->table(new Request(['paket' => $package->id])),
                            'sikap' => $this->sikapRepository->table(new Request(['paket' => $package->id])),
                        ]
                    ]
                ]);
            }
            return $response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
}