<?php

namespace App\Repository;

use App\Entity\Contributor;
use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Faker\Factory;

/**
 * @extends ServiceEntityRepository<Contributor>
 *
 * @method Contributor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contributor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contributor[]    findAll()
 * @method Contributor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContributorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contributor::class);
    }

    public function save(Contributor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Contributor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function checkIfExistAndSave(array $contributors, Project $project): void
    {
        $faker = Factory::create();
        foreach ($contributors as $contributorData) {
            $contributor = $this->findOneBy(['githubName' => $contributorData['login']]);
            if (!$contributor) {
                $contributor = new Contributor();
                $contributor->setName($faker->name());
                $contributor->setGithubAccount($contributorData['html_url']);
                $contributor->setGithubName($contributorData['login']);
            }
            if (!$contributor->getProjects()->contains($project)) {
                $contributor->addProject($project);
                $this->save($contributor, true);
            }
        }
    }

    public function getContributorsInProjectByOrderAlphabetic(Project $project): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.projects', 'p')
            ->where('p.id = :projectId')
            ->setParameter('projectId', $project->getId())
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getNbOfContributorsInProject(Project $project): int
    {
        return $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->leftJoin('c.projects', 'p')
            ->where('p = :project')
            ->setParameter('project', $project)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
