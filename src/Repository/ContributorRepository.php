<?php

namespace App\Repository;

use App\Entity\Contributor;
use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
    public function __construct(ManagerRegistry $registry, private ProjectRepository $projectRepository)
    {
        parent::__construct($registry, Contributor::class);
        $this->projectRepository = $projectRepository;
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
        foreach ($contributors as $contributorData) {
            $contributor = $this->findOneBy(['githubName' => $contributorData['login']]);
            if (!$contributor) {
                $contributor = new Contributor();
                $contributor->setName($contributorData['login']);
                $contributor->setGithubAccount($contributorData['html_url']);
                $contributor->setGithubName($contributorData['login']);
                $this->save($contributor, true);
            }
            if (!$project->getContributors()->contains($contributor)) {
                $project->addContributor($contributor);
                $this->projectRepository->save($project, true);
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
}
