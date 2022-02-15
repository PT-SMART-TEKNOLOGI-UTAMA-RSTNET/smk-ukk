<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeterampilanIndikator extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;
    public function komponenObj(){
        return $this->belongsTo(KeterampilanKomponen::class,'komponen','id');
    }

}
