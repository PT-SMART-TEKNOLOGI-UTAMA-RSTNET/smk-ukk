<?php
/**
 * Created by PhpStorm.
 * User: reyang
 * Date: 2/14/2022
 * Time: 9:26 AM
 */

namespace App\Repositories\Komponen;


use App\Models\CapaianPengetahuan;
use App\Models\PengetahuanIndikator;
use App\Models\PengetahuanKomponen;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class PengetahuanRepository
{
    public function jawab(Request $request){
        try {
            $komponen = PengetahuanKomponen::where('id', $request->komponen)->first();
            $capaian = CapaianPengetahuan::where('ujian', $request->ujian)
                ->where('komponen', $request->komponen)
                ->where('peserta', $request->peserta)
                ->get();
            if ($capaian->count() === 0) {
                $capaian = new CapaianPengetahuan();
                $capaian->id = Uuid::uuid4()->toString();
                $capaian->ujian = $request->ujian;
                $capaian->peserta = $request->peserta;
                $capaian->komponen = $request->komponen;
            } else {
                $capaian = $capaian->first();
            }
            $capaian->answer_content = $request->isi_jawaban;
            $capaian->indikator = $request->indikator;
            if ($komponen->type == 'pg') {
                $capaian->nilai = $capaian->indikator == $komponen->answer ? 1 : 0;
            } else {
                $capaian->nilai = 0;
            }
            $capaian->saveOrFail();
            return $this->table(new Request(['id' => $capaian->komponen, 'peserta' => $request->peserta, 'ujian' => $request->ujian]));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function delete(Request $request){
        try {
            PengetahuanKomponen::where('id', $request->id)->delete();
            return $request->id;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function update(Request $request){
        try {
            $soal = PengetahuanKomponen::where('id', $request->id)->first();
            $soal->content = $request->isi_soal;
            $soal->type = $request->jenis_soal['value'];
            $soal->saveOrFail();
            foreach ($request->pilihan_jawaban as $inputJawaban){
                if ($inputJawaban['is_default'] && strlen($inputJawaban['value']) > 10){
                    $jawaban = PengetahuanIndikator::where('id', $inputJawaban['value'])->first();
                } else {
                    $jawaban = new PengetahuanIndikator();
                    $jawaban->id = Uuid::uuid4()->toString();
                    $jawaban->komponen = $soal->id;
                    $jawaban->nomor = $inputJawaban['nomor'];
                }
                $jawaban->content = $inputJawaban['label'];
                $jawaban->saveOrFail();
                if ($jawaban->nomor == $request->jawaban['nomor']) {
                    $soal->answer = $jawaban->id;
                    $soal->saveOrFail();
                }
            }
            if (collect($request->deleted_jawaban)->count() > 0) {
                foreach ($request->deleted_jawaban as $inputJawaban){
                    PengetahuanIndikator::where('id', $inputJawaban['value'])->delete();
                }
            }
            return $this->table(new Request(['id' => $soal->id]));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function create(Request $request) {
        try {
            $soal = new PengetahuanKomponen();
            $soal->id = Uuid::uuid4()->toString();
            $soal->paket = $request->paket;
            $soal->nomor = $request->nomor;
            $soal->content = $request->isi_soal;
            $soal->type = $request->jenis_soal['value'];
            $soal->saveOrFail();
            foreach ($request->pilihan_jawaban as $inputJawaban){
                $jawaban = new PengetahuanIndikator();
                $jawaban->id = Uuid::uuid4()->toString();
                $jawaban->komponen = $soal->id;
                $jawaban->nomor = $inputJawaban['nomor'];
                $jawaban->content = $inputJawaban['label'];
                $jawaban->saveOrFail();
                if ($jawaban->nomor == $request->jawaban['nomor']) {
                    $soal->answer = $jawaban->id;
                    $soal->saveOrFail();
                }
            }
            return $this->table(new Request(['id' => $soal->id]));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    private function indikatorCollection(PengetahuanKomponen $komponen){
        try {
            $response = collect([]);
            $indikators = $komponen->indikatorCollection;
            foreach ($indikators as $indikator){
                $response->push([
                    'value' => $indikator->id,
                    'label' => $indikator->content,
                    'meta' => [
                        'nomor' => $indikator->nomor,
                        'komponen' => $indikator->komponenObj
                    ]
                ]);
            }
            return $response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function table(Request $request) {
        try {
            $response = collect([]);
            $soals = PengetahuanKomponen::orderBy('nomor','asc')->where('paket', $request->paket);
            if (strlen($request->id) > 0) $soals = $soals->where('id', $request->id);
            //if (strlen($request->paket) > 0) $soals = $soals->where('paket', $request->paket);
            $soals = $soals->get();
            foreach ($soals as $soal){
                $capaian = null;
                if (strlen($request->peserta) > 0 && strlen($request->ujian) > 0) {
                    $capaian = CapaianPengetahuan::where('ujian', $request->ujian)
                        ->where('peserta', $request->peserta)
                        ->where('komponen', $soal->id)
                        ->first();
                    if ($capaian != null){
                        $capaian->indikator = $capaian->indikatorObj;
                    }
                }
                $response->push([
                    'value' => $soal->id,
                    'label' => $soal->content,
                    'meta' => [
                        'paket' => $soal->paketObj,
                        'nomor' => $soal->nomor,
                        'type' => $soal->type,
                        'answer' => $soal->answerObj,
                        'indikator' => $this->indikatorCollection($soal),
                        'capaian' => $capaian
                    ]
                ]);
            }
            return $response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
}