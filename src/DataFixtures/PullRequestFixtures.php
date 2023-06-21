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
            $pullRequest->setName($faker->words(10, true));
            $pullRequest->setUrl('https://github.com');
            $status = $faker->randomElement(['open', 'closed']);
            $pullRequest->setStatus($status);
            $createdAt = $faker->dateTimeBetween('-1 years', 'now');
            $pullRequest->setCreatedAt(DateTimeImmutable::createFromMutable($createdAt));
            // ensure that the pr cannot be merged if it is open
            if ($status === 'open') {
                $pullRequest->setIsMerged(false);
            } else {
                $pullRequest->setIsMerged($faker->boolean);
            }
            // Randomly select a contributor
            $contributor = $this->getReference('contributor_' . rand(0, 10));
            // Choose a project associated with this contributor
            $projects = $contributor->getProjects();
            // Check if the contributor has associated projects
            if (!$projects->isEmpty()) {
                $project = $faker->randomElement($projects->toArray());
                $pullRequest->setProject($project);
                $pullRequest->setContributor($contributor);
                $manager->persist($pullRequest);
            }
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
