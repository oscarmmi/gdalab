<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regions extends Model
{
    protected $table = 'regions';
    protected $primaryKey = 'id_reg';
    
    protected $fillable = [
        'id_reg',
        'description',
        'status'
    ];
}
