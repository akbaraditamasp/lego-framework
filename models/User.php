<?php
namespace Model;

use Illuminate\Database\Eloquent\Model as BaseModel;

class User extends BaseModel
{
    public function logins()
    {
        return $this->hasMany(UserLogin::class);
    }
}
