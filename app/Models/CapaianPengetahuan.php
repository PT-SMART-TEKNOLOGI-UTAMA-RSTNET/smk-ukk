<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapaianPengetahuan extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    public function indikatorObj(){
        return $this->belongsTo(PengetahuanIndikator::class,'indikator','id');
    }
}
