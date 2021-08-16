<?php

namespace App\Containers\UserRegistration\UserContainer\Models;

use App\Ship\Parents\Models\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class AdminModel extends Model implements JWTSubject
{
    protected $table='admin_table';
    protected $fillable = [
        'firstName','lastName','email','mobile','password'
    ];

    protected $attributes = [

    ];

    protected $hidden = [

    ];

    protected $casts = [

    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    /**
     * A resource key to be used in the serialized responses.
     */
    protected string $resourceKey = 'AdminModel';
    
    public function blogs()
    {
        //  return $this->hasMany(BlogModel::class, 'user_id');
        return $this->hasMany('C:\apiato-project\apiato\app\Containers\UserRegistration\UserContainer\Models\BlogModel', 'admin_id');
    }
}
