<?php

namespace App\Repository;

use App\Entity\Contributor;
use App\Entity\Project;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function save(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllGithubLink(): array
    {
        $result = $this->createQueryBuilder('p')
            ->select('p.githubLink')
            ->where('p.isFollowed = :isFollowed')
            ->setParameter('isFollowed', true)
            ->getQuery()
            ->getResult();

        return array_column($result, 'githubLink');
    }

    public function checkIfExistAndSave(array $projects): void
    {
        foreach ($projects as $singleProject) {
            if (!$this->findOneBy(['name' => $singleProject['name']])) {
                $project = new Project();
                $project->setName($singleProject['name']);
                $project->setFullName($singleProject['full_name']);
                $project->setGithubLink($singleProject['html_url']);
                $createAt = $singleProject['created_at'];
                $date = new DateTimeImmutable($createAt);
                $project->setCreatedAt($date);
                $this->save($project, true);
            }
        }
    }

    public function checkAndDeleteNonExistentNames(array $projects): void
    {
        $projectNames = array_column($projects, 'name');
        $existentProjects = $this->findAll();

        foreach ($existentProjects as $existentProject) {
            if (in_array($existentProject->getName(), $projectNames, true)) {
                continue;
            }
            if ($existentProject->isIsFollowed()) {
                continue;
            }
            $this->remove($existentProject, true);
        }
    }

    public function getProjectsInContributorByOrderAlphabetic(Contributor $contributor): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.contributors', 'c')
            ->where('c.id = :contributorId')
            ->setParameter('contributorId', $contributor->getId())
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findFollowedProjects()
    {
        return $this->createQueryBuilder('p')
            ->Where('p.isFollowed = :follow')
            ->setParameter('follow', true)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
