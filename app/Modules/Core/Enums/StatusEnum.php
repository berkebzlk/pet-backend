<?php

namespace App\Modules\Core\Enums;

enum StatusEnum: int
{
    case SUCCESS = 0;
    case ERROR = 1;
    case WARNING = 2;
    case INFO = 3;
    case PENDING = 4;
    case ACCEPTED = 5;
    case REJECTED = 6;

    public function label(): string
    {
        return match ($this) {
            self::SUCCESS => __('status.success'),
            self::ERROR => __('status.error'),
            self::WARNING => __('status.warning'),
            self::INFO => __('status.info'),
            self::PENDING => __('status.pending'),
            self::ACCEPTED => __('status.accepted'),
            self::REJECTED => __('status.rejected'),
        };
    }

    public static function labels(): array
    {
        $map = [];
        foreach (self::cases() as $case) {
            $map[$case->value] = $case->label();
        }
        return $map;
    }
}
