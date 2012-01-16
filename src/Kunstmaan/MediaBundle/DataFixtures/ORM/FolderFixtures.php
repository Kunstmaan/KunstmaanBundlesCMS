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
        $gal = new Folder($manager);
        $gal->setName('media.menu.media');
        $gal->setCanDelete(false);
        $gal->setRel("media");
        $gal->setSequencenumber(1);
        $manager->persist($gal);
        $manager->flush();
        
            $subgal = new ImageGallery($manager);
            $subgal->setParent($gal);
            $subgal->setName('media.menu.images');
            $subgal->setCanDelete(false);
            $subgal->setRel("image");
            $subgal->setSequencenumber(1);
            $manager->persist($subgal);
            $manager->flush();
            
            $subgal = new VideoGallery($manager);
            $subgal->setParent($gal);
            $subgal->setName('media.menu.videos');
            $subgal->setCanDelete(false);
            $subgal->setRel("video");
            $subgal->setSequencenumber(2);
            $manager->persist($subgal);
            $manager->flush();
            
            $subgal = new SlideGallery($manager);
            $subgal->setParent($gal);
            $subgal->setName('media.menu.slides');
            $subgal->setCanDelete(false);
            $subgal->setRel("slideshow");
            $subgal->setSequencenumber(3);
            $manager->persist($subgal);
            $manager->flush();

            $subgal = new FileGallery($manager);
            $subgal->setParent($gal);
            $subgal->setName('media.menu.files');
            $subgal->setCanDelete(false);
            $subgal->setRel("files");
            $subgal->setSequencenumber(4);
            $manager->persist($subgal);
            $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }

}