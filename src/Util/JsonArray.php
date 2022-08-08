<?php declare(strict_types=1);

namespace PriceApi\Util;

use PriceApi\Exception\RuntimeException;
use function is_array;

class JsonArray
{
    public static function decode(string $json): array
    {
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Json decode error: ' . json_last_error_msg());
        }
        if (!is_array($data)) {
            throw new RuntimeException('Json decode error: invalid json');
        }
        return $data;
    }

    public static function encode(array $data): string
    {
        $json = json_encode($data);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Json encode error: ' . json_last_error_msg());
        }
        return $json;
    }
}