<?php
/**
 * Created by PhpStorm.
 * User: reyang
 * Date: 2/11/2022
 * Time: 11:39 AM
 */

namespace App\Repositories\Komponen;


use App\Models\SikapIndikator;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class SikapRepository
{
    public function delete(Request $request){
        try {
            SikapIndikator::where('id', $request->id)->delete();
            return $request->all();
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function update(Request $request){
        try {
            $komponens = SikapIndikator::where('id', $request->id)->first();
            $komponens->indikator = $request->indikator_sikap;
            $komponens->saveOrFail();
            return $request->all();
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function create(Request $request){
        try {
            $komponens = new SikapIndikator();
            $komponens->id = Uuid::uuid4()->toString();
            $komponens->paket = $request->paket;
            $komponens->indikator = $request->indikator_sikap;
            $komponens->saveOrFail();
            return $request->all();
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function table(Request $request) {
        try {
            $response = collect([]);
            $komponens = SikapIndikator::where('paket', $request->paket)->orderBy('indikator','asc')->get();
            foreach ($komponens as $komponen){
                $response->push([
                    'value' => $komponen->id,
                    'label' => $komponen->indikator,
                    'meta' => [
                        'paket' => $komponen->paket
                    ]
                ]);
            }
            return $response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
}