<?php
/**
 * Created by PhpStorm.
 * User: reyang
 * Date: 2/11/2022
 * Time: 12:23 PM
 */

namespace App\Validations;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleValidation
{
    public function delete(Request $request){
        try {
            $valid = Validator::make($request->all(),[
                'id' => 'required|string|exists:ujians,id',
            ]);
            if ($valid->fails()) throw new \Exception(collect($valid->errors()->all())->join("<br>"),400);
            return $request;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),400);
        }
    }
    public function update(Request $request){
        try {
            $request = $request->merge([
                'tanggal_mulai' => Carbon::parse($request->tanggal_mulai)->format('Y-m-d H:i:s'),
                'tanggal_selesai' => Carbon::parse($request->tanggal_selesai)->format('Y-m-d H:i:s')
            ]);
            $valid = Validator::make($request->all(),[
                'id' => 'required|string|exists:ujians,id',
                'jurusan' => 'required|string|exists:' . config('database.erapor') . '.jurusans,id',
                'judul_ujian' => 'required|string|min:3',
                'keterangan_ujian' => 'required|string|min:10',
                'tanggal_mulai' => 'required|date_format:Y-m-d H:i:s|before_or_equal:tanggal_selesai',
                'tanggal_selesai' => 'required|date_format:Y-m-d H:i:s'
            ]);
            if ($valid->fails()) throw new \Exception(collect($valid->errors()->all())->join("<br>"),400);
            if (Carbon::parse($request->tanggal_mulai)->isAfter(Carbon::parse($request->tanggal_selesai))) throw new \Exception('Tanggal selesai tidak boleh lebih dari tanggal mulai',400);
            return $request;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),400);
        }
    }
    public function create(Request $request){
        try {
            $request = $request->merge([
                'tanggal_mulai' => Carbon::parse($request->tanggal_mulai)->format('Y-m-d H:i:s'),
                'tanggal_selesai' => Carbon::parse($request->tanggal_selesai)->format('Y-m-d H:i:s')
            ]);
            $valid = Validator::make($request->all(),[
                'jurusan' => 'required|string|exists:' . config('database.erapor') . '.jurusans,id',
                'judul_ujian' => 'required|string|min:3',
                'keterangan_ujian' => 'required|string|min:10',
                'tanggal_mulai' => 'required|date_format:Y-m-d H:i:s|before_or_equal:tanggal_selesai',
                'tanggal_selesai' => 'required|date_format:Y-m-d H:i:s'
            ]);
            if ($valid->fails()) throw new \Exception(collect($valid->errors()->all())->join("<br>"),400);
            if (Carbon::parse($request->tanggal_mulai)->isAfter(Carbon::parse($request->tanggal_selesai))) throw new \Exception('Tanggal selesai tidak boleh lebih dari tanggal mulai',400);
            return $request;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),400);
        }
    }
}