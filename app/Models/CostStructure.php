<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostStructure extends Model
{
    use HasFactory;

    protected $table = 'cost_structures';

    protected $fillable = [
        'cost_tree',
        'cost_element',
        'cost_centre',
        'cost_code',
        'description'
    ];
}
