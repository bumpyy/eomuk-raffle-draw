<?php

namespace App\Models;

use App\Enum\WinnerPrizeEnum;
use Database\Factories\WinnerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Winner extends Model
{
    /** @use HasFactory<WinnerFactory> */
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'prize' => WinnerPrizeEnum::class,
    ];

    /**
     * Get the submission that owns the Winner
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }
}
