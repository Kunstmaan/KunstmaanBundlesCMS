<?php

namespace Kunstmaan\TranslatorBundle\Model\Translation;

use Kunstmaan\TranslatorBundle\Entity\Translation;

/**
 * Groups all translations for all languages specified by a key
 **/
class TranslationGroup
{
    /**
     * Translation ID
     */
    private $id;

    /**
     * All translations for a specific key (Kunstmaan\TranslatorBundle\Model\Translation\Translation)
     *
     * @var Doctrine\Common\Collections\ArrayCollection
     **/
    private $translations;

    /**
     * Translation identifier
     *
     * @var string
     **/
    private $keyword;

    /**
     * The domain name of this group
     *
     **/
    private $domain;

    public function __construct()
    {
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function hasTranslation($locale)
    {
        if (count($this->translations) <= 0) {
            return false;
        }

        if ($this->getTranslationByLocale($locale) !== null) {
            return true;
        }

        return false;
    }

    public function getTranslationByLocale($locale)
    {
        if (count($this->translations) <= 0) {
            return null;
        }

        foreach ($this->translations as $translation) {
            if (strtolower($translation->getLocale()) == strtolower($locale)) {
                return $translation;
            }
        }

        return null;
    }

    public function getTranslationTextByLocale($locale)
    {
        $translation = $this->getTranslationByLocale($locale);

        return is_null($translation) ? null : $translation->getText();
    }

    public function addTranslation(Translation $translation)
    {
        $translation->setTranslationId($this->getId());
        $this->translations->add($translation);
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function setTranslations($translations)
    {
        foreach ($translations as $translation) {
            $translation->setTranslationId($this->getId());
        }
        $this->translations = $translations;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getKeyword()
    {
        return $this->keyword;
    }

    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
    }
}
