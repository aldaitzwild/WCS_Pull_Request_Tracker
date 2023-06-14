<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use App\Repository\PullRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/pullrequest', name: 'pull_request_')]
class PullRequestController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(PullRequestRepository $pullRequestRepository): Response
    {
        $pullRequests = $pullRequestRepository->findAll();
        return $this->render('pull_request/index.html.twig', [
            'pullRequests' => $pullRequests,

        ]);
    }

    #[Route('/{id<^[0-9]+$>}', methods: ['GET'], name: 'show')]
    public function show(int $id, PullRequestRepository $pullRequestRepository): Response
    {
        $pullRequest = $pullRequestRepository->findOneBy(['id' => $id]);
        if (!$pullRequest) {
            throw $this->createNotFoundException(
                'No pullRequest with id : ' . $id . ' found.'
            );
        }
        return $this->render('pull_request/show.html.twig', [
            'pullRequest' => $pullRequest,
        ]);
    }

    #[Route('/project/{id}', name: 'pullrequestonproject')]
    public function prOnProject(ProjectRepository $projectRepository, int $id, HttpClientInterface $httpClient, SessionInterface $session): Response
    {
        $token = $session->get('user')['access_token'];

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/vnd.github.v3+json',
            'X-GitHub-Api-Version' => '2022-11-28'
        ];

        $project = $projectRepository->findOneBy(['id' => $id]);
        if (!$project) {
            return $this->render('project/index.html.twig');
        }


        $githubUrl = $project->getGithubLink();
        $url = str_replace("github.com", "api.github.com/repos", $githubUrl);
        $url .= "/pulls?state=all";

        $response = $httpClient->request('GET', $url, [
            'headers' => $headers
        ]);

        $statusCode = $response->getStatusCode();



    }
}
