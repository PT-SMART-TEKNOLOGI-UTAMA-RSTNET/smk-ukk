<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $casts = [
        'is_active' => 'boolean'
    ];
    public function jurusanObj(){
        return $this->setConnection(config('database.erapor'))
            ->belongsTo(Jurusan::class,'jurusan','id');
    }
    public function pesertaCollection(){
        return $this->hasMany(Peserta::class,'id','ujian')->orderBy('nopes','asc');
    }
}
