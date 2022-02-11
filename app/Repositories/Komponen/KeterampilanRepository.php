<?php
/**
 * Created by PhpStorm.
 * User: reyang
 * Date: 2/10/2022
 * Time: 2:04 PM
 */

namespace App\Repositories\Komponen;


use App\Models\KeterampilanIndikator;
use App\Models\KeterampilanKomponen;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class KeterampilanRepository
{
    public function delete(Request $request) {
        try {
            KeterampilanKomponen::where('id', $request->id)->delete();
            return $request->all();
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function update(Request $request){
        try {
            $komponen = KeterampilanKomponen::where('id', $request->id)->first();
            $komponen->komponen = $request->komponen['value'];
            $komponen->name = $request->sub_komponen;
            $komponen->nilai_sangat_baik = $request->nilai_sangat_baik;
            $komponen->nilai_baik = $request->nilai_baik;
            $komponen->nilai_cukup = $request->nilai_cukup;
            $komponen->nilai_tidak = $request->nilai_tidak;
            $komponen->penguji_type = $request->penguji['value'];
            $komponen->saveOrFail();

            foreach ($request->indikator as $inputIndikator){
                if (strlen($inputIndikator['value']) > 0) {
                    $indikator = KeterampilanIndikator::where('id', $inputIndikator['value'])->first();
                } else {
                    $indikator = new KeterampilanIndikator();
                    $indikator->id = Uuid::uuid4()->toString();
                }
                $indikator->nomor = $inputIndikator['nomor'];
                $indikator->komponen = $komponen->id;
                $indikator->indikator = $inputIndikator['label'];
                $indikator->saveOrFail();
            }
            if (collect($request->indikator_deleted)->count() > 0) {
                foreach ($request->indikator_deleted as $inputIndikator){
                    KeterampilanIndikator::where('id', $inputIndikator['value'])->delete();
                }
            }
            return $request->all();
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function create(Request $request){
        try {
            $komponen = new KeterampilanKomponen();
            $komponen->id = Uuid::uuid4()->toString();
            $komponen->paket = $request->paket;
            $komponen->komponen = $request->komponen['value'];
            $komponen->name = $request->sub_komponen;
            $komponen->nomor = $request->nomor;
            $komponen->nilai_sangat_baik = $request->nilai_sangat_baik;
            $komponen->nilai_baik = $request->nilai_baik;
            $komponen->nilai_cukup = $request->nilai_cukup;
            $komponen->nilai_tidak = $request->nilai_tidak;
            $komponen->penguji_type = $request->penguji['value'];
            $komponen->saveOrFail();

            foreach ($request->indikator as $inputIndikator){
                $indikator = new KeterampilanIndikator();
                $indikator->id = Uuid::uuid4()->toString();
                $indikator->nomor = $inputIndikator['nomor'];
                $indikator->komponen = $komponen->id;
                $indikator->indikator = $inputIndikator['label'];
                $indikator->saveOrFail();
            }
            return $request->all();
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function table(Request $request){
        try {
            $response = collect([]);
            $keterampilans = KeterampilanKomponen::orderBy('komponen','desc')
                ->where('paket', $request->paket);
            if (strlen($request->id) > 0) $keterampilans = $keterampilans->where('id', $request->id);
            $keterampilans = $keterampilans->get();
            foreach ($keterampilans as $keterampilan){
                $response->push([
                    'value' => $keterampilan->id,
                    'label' => $keterampilan->name,
                    'meta' => [
                        'nomor' => $keterampilan->nomor,
                        'komponen' => $keterampilan->komponen,
                        'type' => $keterampilan->penguji_type,
                        'indikator' => $keterampilan->indikatorCollection,
                        'nilai' => [
                            'sb' => $keterampilan->nilai_sangat_baik,
                            'b' => $keterampilan->nilai_baik,
                            'c' => $keterampilan->nilai_cukup,
                            't' => $keterampilan->nilai_tidak,
                        ]
                    ]
                ]);
            }
            return $response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    private function tableIndikator(KeterampilanKomponen $komponen){
        try {

        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
}