<?php

namespace App\DataFixtures;

use App\Entity\Smartphone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class SmartphoneFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        
        for ($i=0; $i < 10; $i++) { 
            $smartphone = new Smartphone();
            $smartphone->setName('Iphone 12')
                ->setEtat('Reconditionné')
                ->setSysteme('IOS')
                ->setRam('8Go')
                ->setStockage('120Go')
                ->setReseau('5G')
                ->setPrix('50 euros')
                ->setMarque('Iphone')
                ->setTailleEcran('4Pixels');

            $manager->persist($smartphone);
                
            }
           

        $manager->flush();
    }
}
