<?php

namespace App\DataFixtures;

use App\Entity\Contributor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ContributorFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 23; $i++) {
            $contributor = new Contributor();
            $contributor->setName($faker->name);
            $contributor->setGithubAccount('https://github.com');
            $contributor->setGithubName($faker->userName);

            $projects = ProjectFixtures::$projects;
            shuffle($projects);
            $projectCount = random_int(0, min(3, count($projects)));
            $selectedProjects = array_slice($projects, 0, $projectCount);
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
