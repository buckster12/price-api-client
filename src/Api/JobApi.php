<?php declare(strict_types=1);

namespace PriceApi\Api;

use PriceApi\PriceAPI;
use PriceApi\Util\JsonArray;

class JobApi
{
    public const TOPIC_PRODUCT_AND_OFFERS = 'product_and_offers';
    private const JOBS_PATH = '/jobs';
    private PriceAPI $client;

    public function __construct(PriceAPI $client)
    {
        $this->client = $client;
    }

    public function create(array $params = []): ?string
    {
        $request = [
            'source' => $params['source'] ?? 'amazon',
            'country' => $params['country'] ?? 'us',
            'topic' => $params['topic'] ?? self::TOPIC_PRODUCT_AND_OFFERS,
            'key' => $params['key'] ?? 'asin',
            'values' => $params['asin'] ?? '',
        ];
        $response = $this->client->apiRequest(
            'post',
            self::JOBS_PATH,
            ['body' => JsonArray::encode($request)]
        );
        return $response['job_id'] ?? null;
    }

    public function check(string $jobId): ?string
    {
        $response = $this->client->apiRequest(
            'get',
            self::JOBS_PATH . '/' . $jobId
        );
        return $response['status'] ?? null;
    }

    public function download(string $jobId): ?array
    {
        $response = $this->client->apiRequest(
            'get',
            self::JOBS_PATH . '/' . $jobId . '/download'
        );
        return $response ?? null;
    }
}