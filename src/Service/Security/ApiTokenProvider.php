<?php

namespace PRayno\MoveOnRestApi\Security;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ApiTokenProvider
{
    public function __construct(
        private CacheItemPoolInterface $cache,
        private HttpClientInterface $moveonRestApiBaseClient,
        private string $authEndpoint,
        private string $username,
        private string $password,
        private int $expiresIn = 3600
    ) {}

    public function getToken(): string
    {
        $item = $this->cache->getItem('moveonrestapi.token');

        if ($item->isHit()) {
            return $item->get();
        }

        $response = $this->moveonRestApiBaseClient->request('POST', $this->authEndpoint, [
            'json' => ['username' => $this->username, 'password' => $this->password],
        ]);

        $data = $response->toArray();
        $token = $data['data'];

        $item->set($token);
        $item->expiresAfter($this->expiresIn - 60);
        $this->cache->save($item);

        return $token;
    }
}