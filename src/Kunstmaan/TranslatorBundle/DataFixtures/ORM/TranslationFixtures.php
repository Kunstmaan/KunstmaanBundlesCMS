<?php

namespace Kunstmaan\TranslatorBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Kunstmaan\TranslatorBundle\Entity\Translation as Entity;
use Kunstmaan\TranslatorBundle\Model\Translation as Model;

/**
 * Fixture for creation the basic translations
 */
class TranslationFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var Kunstmaan\TranslatorBundle\Repository\TranslationRepository
     */
    protected $repo;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->repo = $manager->getRepository('Kunstmaan\TranslatorBundle\Entity\Translation');

        $helloWorld = new Model;
        $helloWorld->setKeyword('heading.hello_world');
        $helloWorld->setDomain('messages');

        $translations = array(
            'en' => 'Hello World!',
            'fr' => 'Bonjour tout le monde',
            'nl' => 'Hallo wereld!'
        );

        $needForFlush = false;
        foreach ($translations as $language => $text) {
            if ($this->hasFixtureInstalled('messages', 'heading.hello_world', $language)) {
                continue;
            }

            $helloWorld->addText($language, $text);
            $needForFlush = true;
        }

        $this->repo->createTranslations($helloWorld);

        if ($needForFlush === true) {
            $manager->flush();
        }
    }

    /**
     * Checks if the specified translation is installed.
     *
     * @param string $domain
     * @param string $keyword
     * @param string $locale
     *
     * @return bool
     */
    public function hasFixtureInstalled($domain, $keyword, $locale)
    {
        $criteria = array('domain' => $domain, 'keyword' => $keyword, 'locale' => $locale);

        return $this->repo->findOneBy($criteria) instanceof Entity;
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
