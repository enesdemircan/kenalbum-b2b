<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'logo',
        'logo_white',
        'favicon',
        'title',
        'description',
        'phone',
        'address',
        'email',
        'facebook',
        'twitter',
        'instagram',
        'youtube',
        'tax_rate',
        'company_title',
        'announcement'
    ];

    public static function getSettings()
    {
        return static::first() ?? new static();
    }
} 