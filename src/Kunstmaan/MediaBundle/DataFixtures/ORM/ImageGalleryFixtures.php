<?php
// src/Kunstmaan/KAdminBundle/DataFixtures/ORM/BlogFixtures.php

namespace Kunstmaan\KMediaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Kunstmaan\KMediaBundle\Entity\ImageGallery;

class ImageGalleryFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load($manager)
    {
        $gal = new ImageGallery();
        $gal->setName('First image Gallery');
        $manager->persist($gal);
        $manager->flush();

            $subgal = new ImageGallery();
            $subgal->setParent($gal);
            $subgal->setName('Sub of first image Gallery');
            $manager->persist($subgal);
            $manager->flush();

                $subgal3 = new ImageGallery();
                $subgal3->setParent($subgal);
                $subgal3->setName('Sub of the first sub');
                $manager->persist($subgal3);
                $manager->flush();

            $subgal2 = new ImageGallery();
            $subgal2->setParent($gal);
            $subgal2->setName('Second sub of first image Gallery');
            $manager->persist($subgal2);

        $manager->flush();

        $gal2 = new ImageGallery();
        $gal2->setName('Second image Gallery');
        $manager->persist($gal2);
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }

}