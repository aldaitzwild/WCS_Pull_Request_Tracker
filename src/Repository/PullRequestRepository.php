<?php

namespace App\Repository;

use App\Entity\Contributor;
use App\Entity\Project;
use App\Entity\PullRequest;
use DateTimeImmutable;
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

    /**
     * @throws \Exception
     */
    public function checkIfExistAndSave(array $singlePullRequest, Project $project, Contributor|null $contributor): void
    {
        if (!$this->findOneBy(['name' => $singlePullRequest['title']])) {
            $pullRequest = new PullRequest();
            $pullRequest->setName($singlePullRequest['title']);
            $pullRequest->setStatus($singlePullRequest['state']);
            $createAt = $singlePullRequest['created_at'];
            $date = new DateTimeImmutable($createAt);
            $pullRequest->setCreatedAt($date);
            if (!empty($singlePullRequest['merged_at'])) {
                $pullRequest->setIsMerged(true);
            }
            $pullRequest->setUrl($singlePullRequest['html_url']);
            $pullRequest->setProject($project);
            $pullRequest->setContributor($contributor);

            $this->save($pullRequest, true);
        }
    }

    public function checkAndDeleteNonExistentNames(array $pullRequests): void
    {
        $pullRequestsName = array_column($pullRequests, 'title');
        $existentPullRequests = $this->findAll();

        foreach ($existentPullRequests as $existentPullRequest) {
            if (in_array($existentPullRequest->getName(), $pullRequestsName, true)) {
                continue;
            }
            $this->remove($existentPullRequest, true);
        }
    }

    public function findLastPRForProject($project)
    {
        return $this->createQueryBuilder('pr')
            ->where('pr.project = :project')
            ->setParameter('project', $project)
            ->orderBy('pr.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getNbOfPrForContributorInOneProject(Contributor $contributor, Project $project): int
    {
        return $this->createQueryBuilder('pr')
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

    public function getSortedPullRequestsForProject(Project $project): array
    {
        return $this->createQueryBuilder('pr')
            ->where('pr.project = :project')
            ->setParameter('project', $project)
            ->orderBy('pr.status', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getSortedPullRequestsForContributor(Contributor $contributor): array
    {
        return $this->createQueryBuilder('pr')
            ->where('pr.contributor = :contributor')
            ->setParameter('contributor', $contributor)
            ->orderBy('pr.status', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findLastPRForContributor($contributor)
    {
        return $this->createQueryBuilder('pr')
            ->where('pr.contributor = :contributor')
            ->setParameter('contributor', $contributor)
            ->orderBy('pr.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getPullRequestsOrderByStatus(): array
    {
        return $this->createQueryBuilder('pr')
            ->orderBy('pr.status', 'DESC')
            ->addOrderBy('pr.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
