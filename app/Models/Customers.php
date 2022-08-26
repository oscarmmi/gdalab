<?php

namespace App\Models;


use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customers extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'customers';
    protected $primaryKey = 'dni';

    protected $fillable = [
        'dni',
        'id_reg',
        'id_com',
        'email',
        'name',
        'last_name',
        'address',
        'date_reg',
        'status'
    ];

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}