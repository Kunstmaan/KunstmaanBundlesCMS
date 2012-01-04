<?php
// src/Kunstmaan/DemoBundle/DataFixtures/ORM/FileGalleryFixtures.php

namespace Kunstmaan\DemoBundle\DataFixtures\ORM;

use Kunstmaan\MediaBundle\Entity\FileGallery;

use Kunstmaan\MediaBundle\Entity\SlideGallery;

use Kunstmaan\MediaBundle\Entity\VideoGallery;

use Kunstmaan\MediaBundle\Entity\ImageGallery;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Kunstmaan\MediaBundle\Entity\Folder;

class FolderFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load($manager)
    {
        $gal = new Folder();
        $gal->setName('media.menu.media');
        $gal->setCanDelete(false);
        $gal->setRel("media");
        $manager->persist($gal);

            $subgal = new ImageGallery();
            $subgal->setParent($gal);
            $subgal->setName('media.menu.images');
            $subgal->setCanDelete(false);
            $subgal->setRel("image");
            $manager->persist($subgal);
            $manager->flush();
            
            $subgal = new VideoGallery();
            $subgal->setParent($gal);
            $subgal->setName('media.menu.videos');
            $subgal->setCanDelete(false);
            $subgal->setRel("video");
            $manager->persist($subgal);
            $manager->flush();
            
            $subgal = new SlideGallery();
            $subgal->setParent($gal);
            $subgal->setName('media.menu.slides');
            $subgal->setCanDelete(false);
            $subgal->setRel("slideshow");
            $manager->persist($subgal);
            $manager->flush();

            $subgal = new FileGallery();
            $subgal->setParent($gal);
            $subgal->setName('media.menu.files');
            $subgal->setCanDelete(false);
            $subgal->setRel("files");
            $manager->persist($subgal);
            $manager->flush();

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }

}