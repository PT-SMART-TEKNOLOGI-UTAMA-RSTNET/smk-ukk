<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;
    public function userObj(){
        return $this->belongsTo(User::class,'user','id');
    }
    public function jurusanObj(){
        return $this->setConnection(config('database.erapor'))
            ->belongsTo(Jurusan::class,'jurusan','id');
    }
    public function internalObj(){
        return $this->belongsTo(User::class,'penguji_internal','id');
    }
    public function externalObj(){
        return $this->belongsTo(User::class,'penguji_external','id');
    }
    public function paketObj(){
        return $this->belongsTo(PaketSoal::class,'paket','id');
    }
}
