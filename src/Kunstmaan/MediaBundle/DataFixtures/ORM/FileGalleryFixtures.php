<?php
// src/Kunstmaan/KAdminBundle/DataFixtures/ORM/BlogFixtures.php

namespace Kunstmaan\KMediaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Kunstmaan\KMediaBundle\Entity\FileGallery;

class FileGalleryFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load($manager)
    {
        $gal = new FileGallery();
        $gal->setName('First file Gallery');
        $manager->persist($gal);
        $manager->flush();

            $subgal = new FileGallery();
            $subgal->setParent($gal);
            $subgal->setName('Sub of first file Gallery');
            $manager->persist($subgal);
            $manager->flush();

                $subgal3 = new FileGallery();
                $subgal3->setParent($subgal);
                $subgal3->setName('Sub of the first sub');
                $manager->persist($subgal3);
                $manager->flush();

            $subgal2 = new FileGallery();
            $subgal2->setParent($gal);
            $subgal2->setName('Second sub of first file Gallery');
            $manager->persist($subgal2);

        $manager->flush();

        $gal2 = new FileGallery();
        $gal2->setName('Second file Gallery');
        $manager->persist($gal2);
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }

}