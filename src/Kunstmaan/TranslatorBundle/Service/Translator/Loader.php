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
    private $translationRepository;

    /**
     * @{@inheritdoc}
     */
    public function load($resource, $locale, $domain = 'messages')
    {
        $catalogue = new MessageCatalogue($locale);

        $translations = $this->translationRepository->findBy(array('locale' => $locale, 'domain' => $domain));

        foreach ($translations as $translation) {
            $catalogue->set($translation->getKeyword(), $translation->getText(), $domain);
        }

        return $catalogue;
    }

    public function setTranslationRepository($translationRepository)
    {
        $this->translationRepository = $translationRepository;
    }
}
