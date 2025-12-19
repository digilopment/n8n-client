<?php

namespace App\Services;

use App\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class N8NClient
{
    private Client $httpClient;

    private string $currentPath;

    private string $baseUrl;

    private ?string $bearerToken;

    private string $environment;

    public function __construct(?string $baseUrl = null, ?string $bearerToken = null, ?string $environment = null)
    {
        $this->baseUrl = $baseUrl ?? Config::BASE_URL();
        $this->bearerToken = $bearerToken ?? Config::BEARER_TOKEN();
        $this->environment = $environment ?? Config::ENVIRONMENT();

        $this->httpClient = new Client([
            'base_uri' => rtrim($this->baseUrl, '/') . '/',
            'timeout' => 60.0,
            'headers' => $this->bearerToken ? ['Authorization' => 'Bearer ' . $this->bearerToken] : [],
        ]);
    }

    public function workflow(string $name, ?string $environment = null): self
    {
        $env = $environment ?? $this->environment;
        $prefix = ($env === 'production') ? 'webhook' : 'webhook-test';
        $this->currentPath = $prefix . '/' . ltrim($name, '/');
        return $this;
    }

    public function execute(array $payload = []): N8NResponse
    {
        try {
            $response = $this->httpClient->post($this->currentPath, [
                'json' => $payload,
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            return new N8NResponse($body ?? []);
        } catch (GuzzleException $e) {
            error_log('N8N Request Error: ' . $e->getMessage());
            if ($e instanceof RequestException && $e->hasResponse()) {
                $errorBody = $e->getResponse()->getBody()->getContents();
                error_log('N8N Error Response: ' . $errorBody);
                $errorData = json_decode($errorBody, true) ?? ['raw' => $errorBody];
                return new N8NResponse(['error' => true, 'message' => $e->getMessage(), 'details' => $errorData]);
            }
            return new N8NResponse(['error' => true, 'message' => $e->getMessage()]);
        }
    }
}
