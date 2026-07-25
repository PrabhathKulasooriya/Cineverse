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

    protected $fillable = [
        'first_name',
        'last_name',
        'contact_number',
        'email',
        'pending_email',
        'password',
        'status',
        'user_role_iduser_role',
        'email_verified_at',
    ];

    public function UserRole() {
        return $this->belongsTo(UserRole::class, 'user_role_iduser_role');
    }

    public function sendEmailVerificationNotification()
    {
        $url = URL::temporarySignedRoute(
            'verification.verify', now()->addMinutes(60), ['id' => $this->getKey()]
        );

        $recipient = !empty($this->pending_email) ? $this->pending_email : $this->email;

        Mail::to($recipient)->send(new VerifyMail($url));
    }
}