<?php

namespace Kunstmaan\TranslatorBundle\DataFixtures\ORM;

use Kunstmaan\TranslatorBundle\Entity\Translation;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Fixture for creation the basic translations
 */
class TranslationFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $helloWorld = new Translation;
        $helloWorld->setKeyword('heading.hello_world');
        $helloWorld->setLocale('en');
        $helloWorld->setText('Hello World!');
        $helloWorld->setDomain('messages');
        $helloWorld->setCreatedAt(new \DateTime());
        $helloWorld->setFlag(Translation::FLAG_NEW);

        $bonjour = clone $helloWorld;
        $bonjour->setText('Bonjour tout le monde');
        $bonjour->setLocale('fr');

        $hallo = clone $helloWorld;
        $hallo->setText('Hallo wereld!');
        $hallo->setLocale('nl');

        $manager->persist($helloWorld);
        $manager->persist($bonjour);
        $manager->persist($hallo);
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