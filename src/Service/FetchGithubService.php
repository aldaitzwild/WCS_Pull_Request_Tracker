<?php

namespace App\Service;

use App\Repository\ContributorRepository;
use App\Repository\ProjectRepository;
use App\Repository\PullRequestRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class FetchGithubService
{
    private RequestStack $requestStack;
    private HttpClientInterface $httpClient;
    private ProjectRepository $projectRepository;
    private PullRequestRepository $pullRequestRepository;
    private ContributorRepository $contributorRepository;

    public function __construct(
        RequestStack $requestStack,
        HttpClientInterface $httpClient,
        ProjectRepository $projectRepository,
        PullRequestRepository $pullRequestRepository,
        ContributorRepository $contributorRepository,
    ) {
        $this->httpClient = $httpClient;
        $this->requestStack = $requestStack;
        $this->projectRepository = $projectRepository;
        $this->pullRequestRepository = $pullRequestRepository;
        $this->contributorRepository = $contributorRepository;
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

    public function fetchAllPullRequest(): bool
    {
        $session = $this->requestStack->getSession();
        $token = $session->get('user')['access_token'];

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/vnd.github.v3+json',
            'X-GitHub-Api-Version' => '2022-11-28'
        ];

        $githubUrls = $this->projectRepository->findAllGithubLink();
        foreach ($githubUrls as $githubUrl) {
            $url = str_replace("github.com", "api.github.com/repos", $githubUrl);
            $url .= "/pulls?state=all";

            $response = $this->httpClient->request('GET', $url, [
                'headers' => $headers
            ]);
            $statusCode = $response->getStatusCode();

            if (empty($response->toArray())) {
                continue;
            }
            if ($statusCode === 200) {
                $pullRequests = $response->toArray();
                foreach ($pullRequests as $pullRequest) {
                    $project = $this->projectRepository->findOneBy(['githubLink' => $githubUrl]);
                    $contributor = $this->contributorRepository
                        ->findOneBy(['githubName' => $pullRequest['user']['login']]);
                    if (!$contributor) {
                        $contributor = null;
                    }
                    $this->pullRequestRepository->checkIfExistAndSave($pullRequest, $project, $contributor);
                }
            }
            return true;
        }
        return false;
    }
}
