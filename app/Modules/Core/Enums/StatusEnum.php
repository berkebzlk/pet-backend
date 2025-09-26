<?php

namespace App\Modules\Core\Enums;

enum StatusEnum: int
{
    case SUCCESS = 0;
    case ERROR = 1;
    case WARNING = 2;
    case INFO = 3;

    public function label(): string
    {
        return match ($this) {
            self::SUCCESS => __('status.success'),
            self::ERROR => __('status.error'),
            self::WARNING => __('status.warning'),
            self::INFO => __('status.info'),
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
