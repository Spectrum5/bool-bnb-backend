<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_id',
        'message',
        'email',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
}