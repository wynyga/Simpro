<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeramikFloor extends Model
{
    protected $table = 'keramik_floor';

    protected static function booted()
    {
        static::creating(function ($keramikFloor) {
            $lastRecord = self::orderBy('id', 'desc')->first();
            $lastId = $lastRecord ? $lastRecord->id : 0;
            $newId = $lastId + 1;

            $keramikFloor->kode = 'KERM90-' . str_pad($newId, 2, '0', STR_PAD_LEFT);
        });
    }
}

