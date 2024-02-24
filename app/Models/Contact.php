<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'user_id',
        'display_name',
        'phone1',
        'phone2',
        'phone3',
        'email1',
        'email2',
        'email3',
        'address1',
        'address2',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
