<?php

namespace App\DataFixtures;

use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProjectFixtures extends Fixture
{
    public static array $projects = [
        'Alpha',
        'ByteForce',
        'CyberNet',
        'DataWave',
        'CodeGenius',
        'TechVortex',
        'Innovatech',
        'QuantumByte'
    ];

    public function load(ObjectManager $manager): void
    {

        foreach (self::$projects as $singleProject) {
            $project = new Project();
            $project->setName($singleProject);
            $project->setGithubLink('https://github.com');
            $this->addReference('project_' . $singleProject, $project);

            $manager->persist($project);
        }

        $manager->flush();
    }
}
