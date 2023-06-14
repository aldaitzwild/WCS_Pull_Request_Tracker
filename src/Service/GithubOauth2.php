<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use League\OAuth2\Client\Provider\Github;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class GithubOauth2
{
    private Github $github_provider;
    private httpClientInterface $httpClient;

    public function __construct(string $githubId, string $githubSecret)
    {
        $this->github_provider = new Github([
            'clientId' => $githubId,
            'clientSecret' => $githubSecret,
            'redirectUri' => $_ENV['GITHUB_CALLBACK'],
        ]);

    }

    public function getGithubLoginUrl(): string
    {
        $options = [
            'scope' => ['user', 'repo'],
        ];

        return $this->github_provider->getAuthorizationUrl($options);
    }

    public function handleGithubCallback(string $code, SessionInterface $session): array
    {
        try {
            $token = $this->github_provider->getAccessToken('authorization_code', [
                'code' => $code
            ]);

            $user = $this->github_provider->getResourceOwner($token);

            $userArray = $user->toArray();
            $userArray['access_token'] = $token->getToken();

            $session->set('user', $userArray);

            return $userArray;
        } catch (IdentityProviderException $e) {
            throw $e;
        }
    }

    public function getUser(SessionInterface $session): ?array
    {
        return $session->get('user');
    }

    public function isAuthenticated(SessionInterface $session): bool
    {
        return $session->has('user');
    }

    public function logout(SessionInterface $session): void
    {
        $session->remove('user');
    }


}