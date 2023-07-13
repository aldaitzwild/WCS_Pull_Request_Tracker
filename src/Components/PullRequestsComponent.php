<?php

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use App\Repository\PullRequestRepository;

#[AsTwigComponent('pullRequests')]
class PullRequestsComponent
{
    private PullRequestRepository $pullRequestRepository;

    public function __construct(PullRequestRepository $pullRequestRepository)
    {
        $this->pullRequestRepository = $pullRequestRepository;
    }

    public function getPullRequests(): array
    {
        return $this->pullRequestRepository->getAllPullRequestsOpen();
    }
}
