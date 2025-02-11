<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostElement extends Model
{
    protected $table = 'cost_elements';

    protected $fillable = [
        'cost_element_code',
        'cost_centre_code',
        'description'
    ];

    public function costCentre()
    {
        return $this->belongsTo(CostCentre::class, 'cost_centre_code', 'cost_centre_code');
    }
}

