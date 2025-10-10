<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Parserlog
 *  Model for EAC parser logs
 */
class Parserlog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string, DateTime>
     */
    protected $fillable = [
        'type',
        'status',
        'message',
    ];

}
