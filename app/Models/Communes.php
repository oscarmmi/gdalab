<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Communes extends Model
{
    protected $table = 'communes';
    protected $primaryKey = 'id_com';

    protected $fillable = [
        'id_com',
        'id_reg',
        'description',
        'status'
    ];
}
