<?php

namespace PRayno\MoveOnRestApi;

use PRayno\MoveOnRestApi\Security\ApiTokenProvider;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;


class MoveOnRestApiClient implements HttpClientInterface
{
    public function __construct(
        private HttpClientInterface $moveonRestApiBaseClient,
        private ApiTokenProvider $tokenProvider
    ) {}

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $options['headers']['Authorization'] = 'Bearer '.$this->tokenProvider->getToken();

        try {
            $response = $this->moveonRestApiBaseClient->request($method, $url, $options);
            if (401 === $response->getStatusCode()) {
                // Force refresh and retry once
                $this->forceRefreshToken();
                $options['headers']['Authorization'] = 'Bearer '.$this->tokenProvider->getToken();
                $response = $this->moveonRestApiBaseClient->request($method, $url, $options);
            }
            return $response;
        } catch (ClientExceptionInterface $e) {
            throw $e;
        }
    }

    public function stream($responses, float $timeout = null): ResponseStreamInterface
    {
        return $this->moveonRestApiBaseClient->stream($responses, $timeout);
    }

    private function forceRefreshToken(): void
    {

    }

    public function withOptions(array $options): static
    {

    }
}