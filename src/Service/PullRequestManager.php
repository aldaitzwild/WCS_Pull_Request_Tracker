<?php

namespace App\Service;

use App\Entity\Contributor;
use App\Entity\Project;
use App\Repository\ContributorRepository;
use App\Repository\PullRequestRepository;

class PullRequestManager
{
    private $contributorRepository;
    private $pullRequestRepository;

    public function __construct(
        ContributorRepository $contributorRepository,
        PullRequestRepository $pullRequestRepository
    ) {
        $this->contributorRepository = $contributorRepository;
        $this->pullRequestRepository = $pullRequestRepository;
    }

    public function getNbOfPrForContributorInOneProject(Contributor $contributor, Project $project): int
    {
        return $this->pullRequestRepository->createQueryBuilder('pr')
            ->select('count(pr.id)')
            ->where('pr.contributor = :contributor')
            ->andWhere('pr.project = :project')
            ->setParameters([
                'contributor' => $contributor,
                'project' => $project,
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getContributorsWithPRInProject(Project $project): array
    {
        return $this->contributorRepository->createQueryBuilder('c')
            ->innerJoin('c.pullRequests', 'pr')
            ->where('pr.project = :project')
            ->setParameter('project', $project)
            ->getQuery()
            ->getResult();
    }
}
