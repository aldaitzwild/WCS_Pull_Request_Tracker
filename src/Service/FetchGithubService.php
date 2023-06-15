<?php

namespace App\Service;

use App\Repository\ProjectRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class FetchGithubService
{
    private RequestStack $requestStack;
    private HttpClientInterface $httpClient;
    private ProjectRepository $projectRepository;

    public function __construct(
        HttpClientInterface $httpClient,
        ProjectRepository $projectRepository,
        RequestStack $requestStack
    ) {
        $this->httpClient = $httpClient;
        $this->projectRepository = $projectRepository;
        $this->requestStack = $requestStack;
    }

    public function fetchProject(): bool
    {
        $session = $this->requestStack->getSession();
        $token = $session->get('user')['access_token'];

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/vnd.github.v3+json',
            'X-GitHub-Api-Version' => '2022-11-28'
        ];

        $url = 'https://api.github.com/user/repos';

        $response = $this->httpClient->request('GET', $url, [
            'headers' => $headers
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode === 200) {
            $projects = $response->toArray();
            $this->projectRepository->checkAndDeleteNonExistentNames($projects);
            $this->projectRepository->checkIfExistAndSave($projects);

            return true;
        }

        return false;
    }

    public function fetchPullRequest(int $projectId): bool
    {
        $session = $this->requestStack->getSession();
        $token = $session->get('user')['access_token'];

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/vnd.github.v3+json',
            'X-GitHub-Api-Version' => '2022-11-28'
        ];

        $project = $this->projectRepository->findOneBy(['id' => $projectId]);

        $githubUrl = $project->getGithubLink();

        $url = str_replace("github.com", "api.github.com/repos", $githubUrl);
        $url .= "/pulls?state=all";

        $response = $this->httpClient->request('GET', $url, [
            'headers' => $headers
        ]);
        $statusCode = $response->getStatusCode();

        if ($statusCode === 200) {
            $this->projectRepository->checkAndDeleteNonExistentNames($projects);
            $this->projectRepository->checkIfExistAndSave($projects);

            return true;
        }

        return false;
    }
}
