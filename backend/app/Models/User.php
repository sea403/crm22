<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'config' => 'array',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'created_by');
    }

    public function emails()
    {
        return $this->hasMany(Email::class);
    }

    public function getImapConfig()
    {
        $imap = $this->config['email']['imap'] ?? [];

        return [
            'host'          => $imap['host'] ?? null,
            'port'          => $imap['port'] ?? 993,
            'encryption'    => isset($imap['encryption']) && $imap['encryption'] !== 'none' ? $imap['encryption'] : null,
            'validate_cert' => true,
            'username'      => $imap['username'] ?? null,
            'password'      => $imap['password'] ?? null,
            'protocol'      => 'imap',
        ];
    }
}
