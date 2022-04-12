<?php

namespace U2y\Hubspot\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HubspotToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'access_token',
        'refresh_token',
        'expire_at',
    ];

    protected $casts = [
        'expire_at' => 'datetime'
    ];
}