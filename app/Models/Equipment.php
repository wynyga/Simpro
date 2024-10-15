<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'equipment';
    
    protected static function booted()
    {
        static::creating(function ($equipment) {
            // Mengenerate kode EQP20- dengan format yang sesuai
            $lastRecord = self::orderBy('id', 'desc')->first();
            $lastId = $lastRecord ? $lastRecord->id : 0;
            $newId = $lastId + 1;
            
            $equipment->kode = 'EQP20-' . str_pad($newId, 2, '0', STR_PAD_LEFT);
        });
    }
}
