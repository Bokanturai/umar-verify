<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Basic info
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'phone_no',
        'address',
        'photo',
        'pin',
        'profile_photo_url',

        // Identity
        'bvn',
        'nin',
        'birthdate',
        'tin',
        'state',
        'lga',
        'nearest_bus_stop',
        'business_name',

        // Referral system
        'referral_code',
        'referral_bonus',
        'referred_by',
        'performed_by',
        'approved_by',

        // Other
        'claim_id',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }


    /**
     * Relationship: A user has one wallet
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
}
