<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Winner extends Model
{
    /** @use HasFactory<\Database\Factories\WinnerFactory> */
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the submission that owns the Winner
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }
}
