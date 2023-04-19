<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Models
use App\Models\ApartmentDetail;
use App\Models\Image;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title', 
        'slug',
        'lat',
        'lng',
        'address',
        'image',
        'price',
        'visibility',
        'rooms_number', 
        'bathrooms_number',
        'beds_number',
        'description',
        'size',
    ];
    
    public function sponsors()
    {
        return $this->belongsToMany(Sponsor::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function views()
    {
        return $this->hasMany(View::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }
}