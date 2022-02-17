<?php
/**
 * Created by PhpStorm.
 * User: reyang
 * Date: 2/17/2022
 * Time: 8:50 AM
 */

namespace App\Repositories;


use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JurusanRepository
{
    public function table(Request $request) {
        try {
            $respone = collect([]);
            $jurusans = DB::connection(config('database.erapor'))->table('jurusans')->orderBy('name','asc');
            if (strlen($request->id) > 0) $jurusans = $jurusans->where('id', $request->id);
            $jurusans = $jurusans->get();
            foreach ($jurusans as $jurusan){
                $respone->push([
                    'value' => $jurusan->id,
                    'label' => $jurusan->name,
                    'meta' => [
                        'bidang' => $jurusan->bidang,
                        'paket' => $jurusan->kompetensi
                    ]
                ]);
            }
            return $respone;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
}