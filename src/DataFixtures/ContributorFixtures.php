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

            $projectCount = random_int(0, 3);
            for ($j = 0; $j < $projectCount; $j++) {
                $randomProjectIndex = random_int(0, 7);
                $project = $this->getReference('project' . $randomProjectIndex);
                $contributor->addProject($project);
            }

            $this->addReference('contributor_' . $i, $contributor);

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
