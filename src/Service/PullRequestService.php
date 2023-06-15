<?php

namespace App\Service;

use App\Entity\Contributor;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;

class PullRequestService
{
    //define a private property for the EntityManager
    private $entityManager;

    //the constructor of this class takes the instance of entitymanagerinterface
    public function __construct(EntityManagerInterface $entityManager)
    {
        // stock entity manager in a property
        $this->entityManager = $entityManager;
    }

    // method for count the number of pullrequest for a specific contributor in one project
    public function getNbOfPrForContributorInOneProject(Contributor $contributor, Project $project): int
    {
        // build a query with Doctrine querybuilder
        $query = $this->entityManager->createQueryBuilder()
            ->select('count(pr.id)')// count the number of id's of PR
            ->from('App\Entity\PullRequest', 'pr')// select from entity of pullrequest
            ->where('pr.contributor = :contributor')
            ->andWhere('pr.project = :project')
            ->setParameters([
                'contributor' => $contributor,
                'project' => $project,
            ])
            ->getQuery();

        return $query->getSingleScalarResult();
    }
}
