<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoofCeilingTile extends Model
{
    protected $table = 'roof_ceiling_tile';

    protected static function booted()
    {
        static::creating(function ($roofCeilingTile) {
            $lastRecord = self::orderBy('id', 'desc')->first();
            $lastId = $lastRecord ? $lastRecord->id : 0;
            $newId = $lastId + 1;

            $roofCeilingTile->kode = 'RCT80-' . str_pad($newId, 2, '0', STR_PAD_LEFT);
        });
    }
}

