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
    private $translations;

    /**
     * @{@inheritdoc}
     */
    public function load($resource, $locale, $domain = 'messages')
    {
        $catalogue = new MessageCatalogue($locale);

        if (!$this->translations)
        {
            $this->translations = array();
            $translations = $this->translationRepository->findBy(array('domain' => $domain));

            if ($translations)
            {
                foreach ($translations as $trans)
                {
                    if (!key_exists($trans->getLocale(), $this->translations))
                    {
                        $this->translations[$trans->getLocale()] = array();
                    }
                    $this->translations[$trans->getLocale()][] = $trans;
                }
            }
        }
        $translations = $this->translations[$locale];

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
