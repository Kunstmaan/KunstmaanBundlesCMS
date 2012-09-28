<?php

namespace Kunstmaan\MediaBundle\DataFixtures\ORM;

use Kunstmaan\MediaBundle\Helper\RemoteVideo\RemoteVideoHelper;

use Kunstmaan\MediaBundle\Entity\Media;

use Kunstmaan\MediaBundle\Entity\FileGallery;
use Kunstmaan\MediaBundle\Entity\SlideGallery;
use Kunstmaan\MediaBundle\Entity\VideoGallery;
use Kunstmaan\MediaBundle\Entity\ImageGallery;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Kunstmaan\MediaBundle\Entity\Folder;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Fixtures that make a general media-folder for a project
 * and for every type of media a folder in that media-folder
 */
class FolderFixtures extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $gal = new Folder($manager);
        $gal->setName('Media');
        $gal->setTranslatableLocale('en');
        $gal->setRel("media");
        $gal->setSequencenumber(1);
        $manager->persist($gal);
        $manager->flush();

        $gal->setTranslatableLocale('nl');
        $manager->refresh($gal);
        $gal->setName("Media");
        $manager->persist($gal);
        $manager->flush();

        $gal->setTranslatableLocale('fr');
        $manager->refresh($gal);
        $gal->setName("Media");
        $manager->persist($gal);
        $manager->flush();

        $subgal = new Folder($manager);
        $subgal->setParent($gal);
        $subgal->setName('Images');
        $subgal->setTranslatableLocale('en');
        $subgal->setRel("image");
        $subgal->setSequencenumber(1);
        $manager->persist($subgal);
        $manager->flush();

        $subgal->setTranslatableLocale('nl');
        $manager->refresh($subgal);
        $subgal->setName('Afbeeldingen');
        $manager->persist($subgal);
        $manager->flush();

        $subgal->setTranslatableLocale('fr');
        $manager->refresh($subgal);
        $subgal->setName('Images');
        $manager->persist($subgal);
        $manager->flush();

        $subgal = new Folder($manager);
        $subgal->setParent($gal);
        $subgal->setName('Videos');
        $subgal->setTranslatableLocale('en');
        $subgal->setRel("video");
        $subgal->setSequencenumber(2);
        $manager->persist($subgal);
        $manager->flush();

        $subgal->setTranslatableLocale('nl');
        $manager->refresh($subgal);
        $subgal->setName('Video\'s');
        $manager->persist($subgal);
        $manager->flush();

        $subgal->setTranslatableLocale('fr');
        $manager->refresh($subgal);
        $subgal->setName('Vidéos');
        $manager->persist($subgal);
        $manager->flush();

        $subgal = new Folder($manager);
        $subgal->setParent($gal);
        $subgal->setName('Slides');
        $subgal->setTranslatableLocale('en');
        $subgal->setRel("slideshow");
        $subgal->setSequencenumber(3);
        $manager->persist($subgal);
        $manager->flush();

        $subgal->setTranslatableLocale('nl');
        $manager->refresh($subgal);
        $subgal->setName('Presentaties');
        $manager->persist($subgal);
        $manager->flush();

        $subgal->setTranslatableLocale('fr');
        $manager->refresh($subgal);
        $subgal->setName('Presentations');
        $manager->persist($subgal);
        $manager->flush();

        $subgal = new Folder($manager);
        $subgal->setParent($gal);
        $subgal->setName('Files');
        $subgal->setTranslatableLocale('en');
        $subgal->setRel("files");
        $subgal->setSequencenumber(4);
        $manager->persist($subgal);
        $manager->flush();

        $subgal->setTranslatableLocale('nl');
        $manager->refresh($subgal);
        $subgal->setName('Bestanden');
        $manager->persist($subgal);
        $manager->flush();

        $subgal->setTranslatableLocale('fr');
        $manager->refresh($subgal);
        $subgal->setName('Fichiers');
        $manager->persist($subgal);
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }

}