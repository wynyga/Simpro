<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostCentre extends Model
{
    protected $table = 'cost_centres';

    protected $fillable = [
        'cost_centre_code',
        'description',
        'perumahan_id',
        'cost_code',
    ];
}
