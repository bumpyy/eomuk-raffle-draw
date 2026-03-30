<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, InteractsWithMedia, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'phone',
        'social',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'social' => 'array',
            'disqualified' => 'boolean',
        ];
    }

    /**
     * Mutator untuk 'phone'.
     *
     * Secara otomatis mengisi 'phone_formatted'
     * setiap kali 'phone' diatur.
     */
    protected function phone(): Attribute
    {
        return Attribute::make(
            // 'set' akan dipanggil setiap kali Anda melakukan:
            // $contact->phone = '08123...'
            // Contact::create(['phone' => '08123...'])
            set: fn ($value) => [
                'phone' => $value,
                'phone_formatted' => formatPhoneNumber($value),
            ],
        );
    }

    /**
     * Get the masked email attribute.
     */
    public function getMaskedEmailAttribute(): string
    {
        $email = $this->email;

        $em = explode('@', $email);
        $name = implode('@', array_slice($em, 0, count($em) - 1));
        $len = floor(strlen($name) / 2);

        // Mask first half of username, keep second half
        return substr($name, 0, $len).str_repeat('*', $len).'@'.end($em);
    }

    public function getMaskedPhoneAttribute(): string
    {
        $num = $this->phone;

        $length = strlen($num);
        $middlePartLength = (int) floor($length / 2);

        $firstPart = substr($num, 0, $middlePartLength);
        $middlePart = str_repeat('*', 3);
        $lastPart = substr($num, $middlePartLength + 3);

        return $firstPart.$middlePart.$lastPart;
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    // public function sendEmailVerificationNotification()
    // {
    //     $this->notify(new \App\Notifications\VerifyEmailQueued);
    // }

    /**
     * Determine if the user has verified their Whatsapp.
     *
     * @return bool
     */
    public function hasVerifiedPhone()
    {
        return ! is_null($this->phone_verified_at);
    }

    /**
     * Mark the given user's Whatsapp as verified.
     *
     * @return bool
     */
    public function markPhoneAsVerified()
    {
        return $this->forceFill([
            'phone_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Check if the user is verified.
     *
     * A user is verified if either their phone or email is verified.
     *
     * @return bool
     */
    public function isVerified()
    {
        return $this->hasVerifiedPhone() || $this->hasVerifiedEmail();
    }

    /**
     * Check if the user is disqualified.
     *
     * @return bool
     */
    public function isDisqualified()
    {
        return $this->disqualified ?? false;
    }

    /**
     * Get all of the submissions for the User
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Perform any actions required during the boot process of the User model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (! $user->phone_formatted) {
                $user->phone_formatted = formatPhoneNumber($user->phone);
            }
        });
    }
}
