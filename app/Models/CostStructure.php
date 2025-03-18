<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostStructure extends Model
{
    protected $table = 'cost_structures';

    protected $fillable = [
        'cost_tee_code',
        'cost_code',
        'perumahan_id',
        'description'
    ];

    public function costTee()
    {
        return $this->belongsTo(CostTee::class, 'cost_tee_code', 'cost_tee_code');
    }
}

