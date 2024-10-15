<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sanitary extends Model
{
    protected $table = 'sanitary';

    protected static function booted()
    {
        static::creating(function ($sanitary) {
            $lastRecord = self::orderBy('id', 'desc')->first();
            $lastId = $lastRecord ? $lastRecord->id : 0;
            $newId = $lastId + 1;

            $sanitary->kode = 'SANI120-' . str_pad($newId, 2, '0', STR_PAD_LEFT);
        });
    }
}
