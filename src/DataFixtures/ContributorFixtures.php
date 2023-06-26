<?php

namespace App\DataFixtures;

use App\Entity\Contributor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ContributorFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $contributors = [
            ['Anthony PHAM','https://github.com/MisterPingouin','MisterPingouin'],
            ['Max add Flash','https://github.com/maxdlr','maxdlr'],
            ['Benjamin RICHARD','https://github.com/StoicFrenchDev','StoicFrenchDev'],
            ['mouhamed DIOP','https://github.com/Mouhamed114','Mouhamed114'],
            ['Axel Raphael','https://github.com/AxelR972','AxelR972'],
            ['Baptiste Renier','https://github.com/Spolito24','Spolito24'],
            ['Frederic MOUTIN','https://github.com/l00ma','l00ma'],
            ['Aurelien Faure','https://github.com/AurelOconnell','AurelOconnell'],
            ['Thomas Prof','https://github.com/aldaitzwild','aldaitzwild'],
            ['Zack Co-leader','https://github.com/zakaria6907','zakaria6907'],
            ['val leader','https://github.com/val-fr69','val-fr69'],
        ];

        foreach ($contributors as $key => $value) {
            $contributor = new Contributor();
            $contributor->setName($value[0]);
            $contributor->setGithubAccount($value[1]);
            $contributor->setGithubName($value[2]);
            $this->addReference('contributor_' . $key, $contributor);

            $projects = ProjectFixtures::$projects;
            shuffle($projects);

            // Assign exactly 3 projects to each contributor
            $selectedProjects = array_slice($projects, 0, 3);
            foreach ($selectedProjects as $projectName) {
                $project = $this->getReference('project_' . $projectName);
                $contributor->addProject($project);
            }
            $manager->persist($contributor);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProjectFixtures::class,
        ];
    }
}
