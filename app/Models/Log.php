<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'log';
    public $timestamps = false;
    
    protected $fillable = [
        'ip',
        'input',
        'output',
        'created_at'
    ];
}
