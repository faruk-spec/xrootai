<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OAuthProvider extends Model
{
    protected $table = 'oauth_providers';

    protected $fillable = [
        'provider_name',
        'provider_slug',
        'client_id',
        'client_secret',
        'redirect_url',
        'auth_url',
        'token_url',
        'user_info_url',
        'scopes',
        'additional_params',
        'is_active',
    ];

    protected $casts = [
        'client_secret' => 'encrypted',
        'scopes' => 'array',
        'additional_params' => 'array',
        'is_active' => 'boolean',
    ];
}
