<?php

namespace App\Services\OAuth;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class DynamicCustomProvider extends AbstractProvider implements ProviderInterface
{
    protected string $authUrl;
    protected string $tokenUrl;
    protected string $userInfoUrl;

    public function __construct($request, $clientId, $clientSecret, $redirectUrl, string $authUrl, string $tokenUrl, string $userInfoUrl, array $scopes = [])
    {
        parent::__construct($request, $clientId, $clientSecret, $redirectUrl);
        $this->authUrl = $authUrl;
        $this->tokenUrl = $tokenUrl;
        $this->userInfoUrl = $userInfoUrl;
        $this->scopes = $scopes;
    }

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase($this->authUrl, $state);
    }

    protected function getTokenUrl(): string
    {
        return $this->tokenUrl;
    }

    protected function getUserByToken($token): array
    {
        $response = $this->getHttpClient()->get($this->userInfoUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true) ?: [];
    }

    protected function mapUserToObject(array $user): User
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'] ?? ($user['sub'] ?? ($user['uid'] ?? null)),
            'nickname' => $user['nickname'] ?? ($user['username'] ?? null),
            'name' => $user['name'] ?? ($user['display_name'] ?? ($user['first_name'] ?? 'User')),
            'email' => $user['email'] ?? null,
            'avatar' => $user['avatar'] ?? ($user['picture'] ?? ($user['avatar_url'] ?? null)),
        ]);
    }
}
