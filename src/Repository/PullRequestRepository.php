<?php

namespace App\Repository;

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
}
