<?php

namespace App\DataFixtures;

use App\Entity\Contributor;
use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ContributorFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 11; $i++) {
            $contributor = new Contributor();
            $contributor->setName($faker->name);
            $contributor->setGithubAccount('https://github.com');
            $contributor->setGithubName($faker->userName);

            if ($i < 3) {
                $contributor->addProject($this->getReference('project_0'));
            } elseif ($i < 6) {
                $contributor->addProject($this->getReference('project_1'));
            } else {
                $contributor->addProject($this->getReference('project_2'));
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
