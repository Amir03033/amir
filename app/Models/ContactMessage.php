<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'message',
        'mail_sent',
        'read_at',
    ];

    protected $casts = [
        'mail_sent' => 'boolean',
        'read_at' => 'datetime',
    ];
}
