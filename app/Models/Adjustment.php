<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Adjustment
 * Model for EAC Fuel Adjustment prices.
 */
class Adjustment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string, DateTime>
     */
    protected $fillable = [
        'start_date',
        'end_date',
        'consumer_type',
        'weighted_average_fuel_price',
        'fuel_adjustment_coefficient',
        'voltage_type',
        'total',
        'fuel',
        'co2_emissions',
        'cosmos',
        'revised_fuel_adjustment_price',
        'source',
        'source_name',
        'user_id',

    ];

    /**
     * Get the user that owns the adjustment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
