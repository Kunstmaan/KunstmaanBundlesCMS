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

    private $catalogues = [];

    /**
     * @return MessageCatalogue
     */
    public function load($resource, $locale, $domain = 'messages')
    {
        if (!isset($this->catalogues[$locale])) {
            $catalogue = new MessageCatalogue($locale);
            $translations = $this->translationRepository->findBy(['locale' => $locale]);
            foreach ($translations as $translation) {
                $catalogue->set(
                    (string) $translation->getKeyword(),
                    (string) $translation->getText(),
                    (string) $translation->getDomain()
                );
            }
            $this->catalogues[$locale] = $catalogue;
        } else {
            $catalogue = $this->catalogues[$locale];
        }

        return $catalogue;
    }

    public function setTranslationRepository($translationRepository)
    {
        $this->translationRepository = $translationRepository;
    }
}
