<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider',
        'encrypted_key',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getEncryptedKeyAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }
        try {
            return \Illuminate\Support\Facades\Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function setEncryptedKeyAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['encrypted_key'] = $value;
            return;
        }
        try {
            \Illuminate\Support\Facades\Crypt::decryptString($value);
            $this->attributes['encrypted_key'] = $value;
        } catch (\Exception $e) {
            $this->attributes['encrypted_key'] = \Illuminate\Support\Facades\Crypt::encryptString($value);
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
