<?php
/** @noinspection MagicMethodsValidityInspection */
declare(strict_types=1);

namespace PriceApi;

use PriceApi\Api\JobApi;
use Psr\Http\Message\ResponseInterface;

class PriceAPI
{
    private const BASE_URL = 'https://api.priceapi.com/v2';
    private const USER_AGENT = 'price-api-client/0.1.0';

    /**
     * @var \PriceApi\Http\HttpClientInterface
     */
    protected $client;

    public function __construct(array $settings = [])
    {
        $this->apiKey = $settings['api_key'] ?? '';
        $this->client = $settings['client'] ?? new \GuzzleHttp\Client();
    }

    public function apiRequest(string $method, string $path, array $options = []): array
    {
        $url = sprintf('%s%s?token=%s', self::BASE_URL, $path, $this->apiKey);
        $options['headers']['User-Agent'] = self::USER_AGENT;
        $options['headers']['Accept'] = 'application/json';
        $options['headers']['Content-Type'] = 'application/json';
        $options['http_errors'] = false;

        try {
            $response = $this->client->request($method, $url, $options);
            $responseArray = \PriceApi\Http\ResponseHandler::getContent($response);
        } catch (\GuzzleHttp\Exception\GuzzleException $exception) {
            throw new \PriceApi\Exception\RuntimeException($exception->getMessage());
        }

        if ($response->getStatusCode() !== 200) {
            if ($responseArray['reason'] === 'unauthorized') {
                throw new \PriceApi\Exception\UnauthorizedException($responseArray['comment']);
            }
            throw new \PriceApi\Exception\RuntimeException($responseArray['reason'] . ': ' . $responseArray['comment']);
        }

        return $responseArray;
    }

    public function job(): \PriceApi\Api\JobApi
    {
        return new JobApi($this);
    }

    protected string $apiKey;
    protected \Psr\Http\Client\ClientInterface $guzzleClient;
    protected const URL_JOBS = 'https://api.priceapi.com/v2/jobs';

    public function setHttpClient(\Psr\Http\Client\ClientInterface $client): self
    {
        $this->guzzleClient = $client;
        return $this;
    }

    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function post(string $url, array $request, array $options = []): ResponseInterface
    {
        $url .= '?token=' . $this->apiKey;
        return $this->guzzleClient->request(
            'POST',
            $url,
            [
                'body' => json_encode($request),
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function get(string $url, array $options = []): ResponseInterface
    {
        $url .= '?token=' . $this->apiKey;
        return $this->guzzleClient->request(
            'GET',
            $url,
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);
    }
}
