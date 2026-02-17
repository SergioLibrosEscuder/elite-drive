<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $table = 'reservations';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'start_date',
        'end_date',
        'amount',
        'status',
    ];

    // Set special types of fields
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    // Get associated user of the reservation
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Get associated vehicle of the reservation
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
