<?php

namespace App\Models;

use Guava\Calendar\Contracts\Eventable;
use Guava\Calendar\ValueObjects\Event;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model implements Eventable
{
    use HasFactory;

    protected $fillable=[
        'title',
        'color',
        'description',
        'starts_at',
        'ends_at'
    ];

}
