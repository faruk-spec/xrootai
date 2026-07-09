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
        'scopes' => 'array',
        'additional_params' => 'array',
        'is_active' => 'boolean',
    ];

    public function getClientSecretAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }
        try {
            return \Illuminate\Support\Facades\Crypt::decryptString($value);
        } catch (\Exception $e) {
            // If MAC is invalid due to APP_KEY change or plain-text storage, return raw value safely
            return $value;
        }
    }

    public function setClientSecretAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['client_secret'] = $value;
            return;
        }
        try {
            \Illuminate\Support\Facades\Crypt::decryptString($value);
            $this->attributes['client_secret'] = $value;
        } catch (\Exception $e) {
            $this->attributes['client_secret'] = \Illuminate\Support\Facades\Crypt::encryptString($value);
        }
    }
}
