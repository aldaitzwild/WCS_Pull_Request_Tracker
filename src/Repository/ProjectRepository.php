<?php

namespace App\Repository;

use App\Entity\Project;
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

    public function checkIfExistAndSave(array $projects): void
    {
        foreach ($projects as $singleProject) {
            if (!$this->findOneBy(['name' => $singleProject['name']])) {
                $project = new Project();
                $project->setName($singleProject['name']);
                $project->setGithubLink($singleProject['html_url']);
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
            $this->remove($existentProject, true);
        }
    }
}
