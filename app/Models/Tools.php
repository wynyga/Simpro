<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tools extends Model
{
    protected $table = 'tools';

    protected static function booted()
    {
        static::creating(function ($tools) {
            $lastRecord = self::orderBy('id', 'desc')->first();
            $lastId = $lastRecord ? $lastRecord->id : 0;
            $newId = $lastId + 1;

            $tools->kode = 'EQT30-' . str_pad($newId, 2, '0', STR_PAD_LEFT);
        });
    }
}
