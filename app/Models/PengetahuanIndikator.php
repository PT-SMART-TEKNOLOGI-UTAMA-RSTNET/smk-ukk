<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengetahuanIndikator extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;
    public function komponenObj(){
        return $this->belongsTo(PengetahuanKomponen::class,'komponen','id');
    }
}
