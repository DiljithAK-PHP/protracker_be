<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    public $timestamps = false;

    use HasFactory;
    
    protected $fillable = [
        'email', 'token', 'created_at', 'expires_at',
    ];

    protected $dates = ['expires_at'];
}
