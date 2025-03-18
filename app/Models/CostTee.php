<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostTee extends Model
{
    protected $table = 'cost_tees';

    protected $fillable = [
        'cost_tee_code',
        'cost_element_code',
        'perumahan_id',
        'description'
    ];

    public function costElement()
    {
        return $this->belongsTo(CostElement::class, 'cost_element_code', 'cost_element_code');
    }
}

