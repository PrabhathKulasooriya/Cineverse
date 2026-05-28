<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;


    protected $table= 'master_user';
    protected  $primaryKey='idmaster_user';


    public function UserRole(){

        return $this->belongsTo(UserRole::class,'user_role_iduser_role');
    }





}




