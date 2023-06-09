<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PullRequestRepository;

class PullRequestController extends AbstractController
{
    #[Route('/pull/request', name: 'app_pull_request')]
    public function index(): Response
    {
        return $this->render('pull_request/index.html.twig', [
            'controller_name' => 'PullRequestController',
        ]);
    }

    #[Route('/pullRequest/{id<^[0-9]+$>}', methods: ['GET'], name: 'pullRequest_show')]
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
}
