<?php

namespace App\DataFixtures;

use App\Entity\PullRequest;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PullRequestFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 50; $i++) {
            $pullRequest = new PullRequest();
            $pullRequest->setName($faker->words(3, true));
            $pullRequest->setUrl('https://github.com/pr/' . $faker->randomNumber(5));
            $pullRequest->setStatus($faker->randomElement(['open', 'closed']));
            $createdAt = $faker->dateTimeBetween('-1 years', 'now');
            $pullRequest->setCreatedAt(DateTimeImmutable::createFromMutable($createdAt));

            $pullRequest->setIsMerged($faker->boolean);

            // On suppose qu'il y a au moins un projet et un contributeur
            $project = $this->getReference('project_' . $faker->randomElement(ProjectFixtures::$projects));
            $pullRequest->setProject($project);

            $contributor = $this->getReference('contributor_' . rand(0, 22));
            $pullRequest->setContributor($contributor);

            $manager->persist($pullRequest);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProjectFixtures::class,
            ContributorFixtures::class,
        ];
    }
}