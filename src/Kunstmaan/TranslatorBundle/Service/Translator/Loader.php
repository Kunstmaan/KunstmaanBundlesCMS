<?php

namespace Kunstmaan\TranslatorBundle\Service\Translator;

use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Translation Loader
 * Loads translations from the defined stasher
 */
class Loader implements LoaderInterface
{
    /**
     * Stasher for storing/retrieving translations
     * @var Kunstmaan\TranslatorBundle\Service\Stasher\StasherInterface
     */
    private $stasher;

    /**
     * @{@inheritdoc}
     */
    public function load($resource, $locale, $domain = 'messages')
    {
        $catalogue = new MessageCatalogue($locale);

        $translations = $this->stasher->getTranslationsByLocaleAndDomain($locale, $domain);

        foreach ($translations as $translation) {
            $catalogue->set($translation->getKeyword(), $translation->getText(), $domain);
        }

        return $catalogue;
    }

    public function setStasher(\Kunstmaan\TranslatorBundle\Service\Stasher\StasherInterface $stasher)
    {
        $this->stasher = $stasher;
    }
}
