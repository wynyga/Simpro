<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PipingPump extends Model
{
    protected $table = 'piping_pump';

    protected static function booted()
    {
        static::creating(function ($pipingPump) {
            $lastRecord = self::orderBy('id', 'desc')->first();
            $lastId = $lastRecord ? $lastRecord->id : 0;
            $newId = $lastId + 1;

            $pipingPump->kode = 'PIPP130-' . str_pad($newId, 2, '0', STR_PAD_LEFT);
        });
    }
}

