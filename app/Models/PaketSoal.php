<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketSoal extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;

    public function jurusanObj(){
        return $this->setConnection(config('database.erapor'))
            ->belongsTo(Jurusan::class,'jurusan','id');
    }
}
