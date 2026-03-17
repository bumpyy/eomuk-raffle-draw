<?php

namespace App\Models;

use App\Enum\StoreEnum;
use App\Enum\SubmissionStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Submission extends Model implements HasMedia
{
    /** @use HasFactory<SubmissionFactory> */
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id', 'created_at', 'updated_at', 'uuid',
    ];

    // only the `updated` event will get logged automatically
    protected static $recordEvents = ['updated'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => SubmissionStatusEnum::class,
        'store_name' => StoreEnum::class,
    ];

    /**
     * Register the media collections
     */
    public function registerMediaCollections(): void
    {

        $this->addMediaCollection('submission')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpg', 'image/jpeg', 'image/png']);

    }

    /**
     * Get the user that owns the Submission
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($submission) {

            if (! $submission->uuid) {
                do {
                    $uuid = generateUniqueCode('', 3, 3);
                } while (Submission::where('uuid', $uuid)->exists());

                $submission->uuid = $uuid;
            }
        });
    }
}
