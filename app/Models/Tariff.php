<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Tariff
 *  Model for EAC Tariff prices.
 */
class Tariff extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string, \DateTime>
     */
    protected $fillable = [
        'start_date',
        'end_date',
        'code',
        'recurring_supply_charge',
        'recurring_meter_reading',
        'energy_charge_normal',
        'energy_charge_reduced',
        'network_charge_normal',
        'network_charge_reduced',
        'ancillary_services_normal',
        'ancillary_services_reduced',
        'energy_charge_subsidy_first',
        'energy_charge_subsidy_second',
        'energy_charge_subsidy_third',
        'supply_subsidy_first',
        'supply_subsidy_second',
        'supply_subsidy_third',
        'source',
        'source_name',
        'user_id',
    ];

    /**
     * Get the user that owns the tariff.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
