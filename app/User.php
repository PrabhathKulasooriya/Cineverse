<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;


use App\Mail\VerifyMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    protected $table = 'master_user';
    protected $primaryKey = 'idmaster_user';

    public function UserRole() {
        return $this->belongsTo(UserRole::class, 'user_role_iduser_role');
    }

    public function sendEmailVerificationNotification()
    {
        $url = URL::temporarySignedRoute(
            'verification.verify', now()->addMinutes(60), ['id' => $this->getKey()]
        );

        Mail::to($this->email)->send(new VerifyMail($url));
    }
}