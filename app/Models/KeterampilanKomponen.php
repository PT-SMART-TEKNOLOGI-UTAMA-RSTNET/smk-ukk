<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeterampilanKomponen extends Model
{
    use HasFactory;
    protected $table = 'keterampilan_komponents';
    protected $keyType = 'string';
    public $incrementing = false;

    public function indikatorCollection(){
        return $this->hasMany(KeterampilanIndikator::class,'komponen','id');
    }
}
