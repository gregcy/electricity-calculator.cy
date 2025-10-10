<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Cost
 *  Model for EAC Other Costs prices.
 */
class Cost extends Model
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
        'name',
        'code',
        'prefix',
        'suffix',
        'value',
        'source',
        'source_name',
        'user_id',
    ];

    /**
     * Get the user that owns the cost.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
