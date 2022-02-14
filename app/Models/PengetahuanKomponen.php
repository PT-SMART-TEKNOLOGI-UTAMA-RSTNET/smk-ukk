<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengetahuanKomponen extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;
    public function paketObj(){
        return $this->belongsTo(PaketSoal::class,'paket','id');
    }
    public function answerObj(){
        return $this->belongsTo(PengetahuanIndikator::class,'answer','id');
    }
    public function indikatorCollection(){
        return $this->hasMany(PengetahuanIndikator::class,'komponen','id')->orderBy('nomor','asc');
    }
}
