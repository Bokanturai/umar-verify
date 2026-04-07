<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = 'report';

    protected $fillable = [
        'user_id',
        'account_number',
        'account_name',
        'bank_code',
        'bank_name',
        'is_verified',
        'verified_at',
        'amount',
        'phone_number',
        'network',
        'ref',
        'status',
        'type',
        'description',
        'old_balance',
        'new_balance',
        'service_id'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}