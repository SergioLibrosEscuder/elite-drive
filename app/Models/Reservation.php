<?php
// Sergio Libros

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    // Define the table associated with the model
    protected $table = 'reservations';
    // Define the primary key of the table
    protected $primaryKey = 'id';
    // Enable timestamps for created_at and updated_at fields
    public $timestamps = true;
    // Define fillable fields for mass assignment
    protected $fillable = [
        'user_id',
        'vehicle_id',
        'start_date',
        'end_date',
        'amount',
        'status',
    ];

    // Define casts for date and amount fields
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
