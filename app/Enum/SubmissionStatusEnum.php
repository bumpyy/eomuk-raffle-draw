<?php

namespace App\Enum;

enum SubmissionStatusEnum: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';

    public function getDescription(): ?string
    {
        return match ($this) {
            self::PENDING => 'Submission kamu sedang direview oleh tim kami',
            self::ACCEPTED => 'Submission kamu telah diverifikasi oleh tim kami',
            self::REJECTED => 'Submission kamu ditolak oleh tim kami',
        };
    }
}
