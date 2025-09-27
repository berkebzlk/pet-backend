<?php

namespace App\Modules\Core\Enums;

enum HttpStatusEnum: int
{
    case OK = 200;
    case CREATED = 201;
    case NO_CONTENT = 204;
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case METHOD_NOT_ALLOWED = 405;
    case UNPROCESSABLE_ENTITY = 422;
    case INTERNAL_SERVER_ERROR = 500;   
    case SERVICE_UNAVAILABLE = 503;
    case GATEWAY_TIMEOUT = 504;

    public function label(): string
    {
        static $httpLabels = null;
        if ($httpLabels === null) {
            $httpLabels = trans('http');
        }

        return match ($this) {
            self::OK => $httpLabels['success'] ?? '',
            self::CREATED => $httpLabels['created'] ?? '',
            self::NO_CONTENT => $httpLabels['no_content'] ?? '',
            self::BAD_REQUEST => $httpLabels['bad_request'] ?? '',
            self::UNAUTHORIZED => $httpLabels['unauthorized'] ?? '',
            self::FORBIDDEN => $httpLabels['forbidden'] ?? '',
            self::NOT_FOUND => $httpLabels['not_found'] ?? '',
            self::METHOD_NOT_ALLOWED => $httpLabels['method_not_allowed'] ?? '',
            self::UNPROCESSABLE_ENTITY => $httpLabels['unprocessable_entity'] ?? '',
            self::INTERNAL_SERVER_ERROR => $httpLabels['internal_server_error'] ?? '',
            self::SERVICE_UNAVAILABLE => $httpLabels['service_unavailable'] ?? '',
            self::GATEWAY_TIMEOUT => $httpLabels['gateway_timeout'] ?? '',
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
