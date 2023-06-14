<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use League\OAuth2\Client\Provider\Github;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class GithubOauth2
{
    private Github $githubProvider;

    public function __construct(string $githubId, string $githubSecret)
    {
        $this->githubProvider = new Github([
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

        return $this->githubProvider->getAuthorizationUrl($options);
    }

    public function handleGithubCallback(string $code, SessionInterface $session): array
    {
        try {
            $token = $this->githubProvider->getAccessToken('authorization_code', [
                'code' => $code
            ]);

            $user = $this->githubProvider->getResourceOwner($token);

            $userArray = $user->toArray();
            $userArray['access_token'] = $token->getToken();

            $session->set('user', $userArray);

            return $userArray;
        } catch (IdentityProviderException $e) {
            throw $e;
        }
    }
}
