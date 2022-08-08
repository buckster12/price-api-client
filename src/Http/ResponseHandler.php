<?php declare(strict_types=1);

namespace PriceApi\Http;

use PriceApi\Exception\RuntimeException;
use PriceApi\Util\JsonArray;
use Psr\Http\Message\ResponseInterface;

class ResponseHandler
{
    public static function getContent(ResponseInterface $response): array
    {
        $body = $response->getBody()->getContents();
        if (empty($body)) {
            return [];
        }
        // Check if the response is JSON
        if (strpos($response->getHeaderLine('Content-Type'), 'application/json') === false) {
            throw new RuntimeException('Response is not JSON');
        }

        return JsonArray::decode($body);
    }
}