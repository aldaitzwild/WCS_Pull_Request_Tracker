<?php

namespace App\Repository;

use App\Entity\Contributor;
use App\Entity\Project;
use App\Entity\PullRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PullRequest>
 *
 * @method PullRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method PullRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method PullRequest[]    findAll()
 * @method PullRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PullRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PullRequest::class);
    }

    public function save(PullRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PullRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllNames(): array
    {
        $result = $this->createQueryBuilder('u')
            ->select('u.name')
            ->getQuery()
            ->getResult();

        return array_column($result, 'name');
    }

    public function checkIfExistAndSave(array $pullRequests, Project $project, Contributor $contributor): void
    {
        foreach ($pullRequests as $singlePullRequest) {
            if (!$this->findOneBy(['name' => $singlePullRequest['name']])) {
                $pullRequest = new PullRequest();
                $pullRequest->setName($singlePullRequest['name']);
                $pullRequest->setStatus($singlePullRequest['state']);
                $pullRequest->setCreatedAt($singlePullRequest['created_at']);
                if (!empty($singlePullRequest['merged_at'])) {
                    $pullRequest->setIsMerged(true);
                }
                $pullRequest->setUrl($singlePullRequest['html_url']);

                $pullRequest->setProject($project);
                $pullRequest->setContributor($contributor);

                $this->save($pullRequest, true);
            }
        }
    }

    public function checkAndDeleteNonExistentNames(array $pullRequestsName): void
    {
        $pullRequestsName = array_column($pullRequestsName, 'name');
        $existentPullRequests = $this->findAll();

        foreach ($existentPullRequests as $existentPullRequest) {
            if (in_array($existentPullRequest->getName(), $pullRequestsName, true)) {
                continue;
            }
            $this->remove($existentPullRequest, true);
        }
    }
}
