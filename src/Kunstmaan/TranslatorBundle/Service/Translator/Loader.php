<?php

namespace Kunstmaan\TranslatorBundle\Service\Translator;

use Kunstmaan\TranslatorBundle\Entity\TranslationDomain;
use Kunstmaan\TranslatorBundle\Entity\Translation;
use Kunstmaan\TranslatorBundle\Model\Translation\TranslationGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

class Loader implements LoaderInterface
{
    private $stasher;

    public function load($resource, $locale, $domain = 'messages'){

        $catalogue = new MessageCatalogue($locale);

        $translations = $this->stasher->getTranslationsByLocaleAndDomain($locale, $domain);

        foreach ($translations as $translation) {
            $catalogue->set($translation->getKeyword(), $translation->getText(), $domain);
        }

        return $catalogue;
    }

    public function setStasher($stasher)
    {
        $this->stasher = $stasher;
    }
}
