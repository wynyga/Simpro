<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blok extends Model
{
    use HasFactory;

    protected $table = 'blok';

    protected $fillable = [
        'nama_blok',
        'perumahan_id'
    ];

    public function perumahan()
    {
        return $this->belongsTo(Perumahan::class, 'perumahan_id');
    }

    public function units()
    {
        return $this->hasMany(Unit::class, 'blok_id');
    }
}
