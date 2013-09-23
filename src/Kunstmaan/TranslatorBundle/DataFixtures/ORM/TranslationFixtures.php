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
        $helloWorld->setDomain('messages');
        $helloWorld->setCreatedAt(new \DateTime());
        $helloWorld->setFlag(Translation::FLAG_NEW);

        $needForFlush = false;

        if (!$this->hasFixtureInstalled($manager, 'messages', 'heading.hello_world', 'en')) {
            $helloWorld->setLocale('en');
            $helloWorld->setText('Hello World!');
            $manager->persist($helloWorld);
            $needForFlush = true;
        }

        if (!$this->hasFixtureInstalled($manager, 'messages', 'heading.hello_world', 'fr')) {
            $bonjour = clone $helloWorld;
            $bonjour->setText('Bonjour tout le monde');
            $bonjour->setLocale('fr');
            $manager->persist($bonjour);
            $needForFlush = true;
        }

        if (!$this->hasFixtureInstalled($manager, 'messages', 'heading.hello_world', 'nl')) {
            $hallo = clone $helloWorld;
            $hallo->setText('Hallo wereld!');
            $hallo->setLocale('nl');
            $manager->persist($hallo);
            $needForFlush = true;
        }

        if ($needForFlush === true) {
            $manager->flush();
        }
    }

    public function hasFixtureInstalled(ObjectManager $manager, $domain, $keyword, $locale)
    {
        return $manager->getRepository('Kunstmaan\TranslatorBundle\Entity\Translation')->findOneBy(array('domain' => $domain, 'keyword' => $keyword, 'locale' => $locale)) instanceof \Kunstmaan\TranslatorBundle\Entity\Translation;
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