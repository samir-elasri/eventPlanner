<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Registration;
use App\Models\User;

class Event extends Model
{
    use hasFactory;

    protected $fillable = [
        'name',
        'start_datetime', 
        'duration',
        'description',
        'location',
        'capacity',
        'waitlist_capacity',
        'status'
    ];
    
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function users()
    {
        return $this->hasManyThrough(User::class, Registration::class);
    }
}
