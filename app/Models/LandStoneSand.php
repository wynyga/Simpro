<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandStoneSand extends Model
{
    protected $table = 'land_stone_sand';

    protected static function booted()
    {
        static::creating(function ($landStoneSand) {
            $lastRecord = self::orderBy('id', 'desc')->first();
            $lastId = $lastRecord ? $lastRecord->id : 0;
            $newId = $lastId + 1;

            $landStoneSand->kode = 'LSS40-' . str_pad($newId, 2, '0', STR_PAD_LEFT);
        });
    }
}
