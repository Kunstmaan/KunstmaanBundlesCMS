<?php
namespace Kunstmaan\TranslatorBundle\Model\Translation;

/**
 * Groups all translations for all languages specified by a key
 **/
class TranslationGroup
{
    /**
     * All translations for the key
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
     * The domain of this group
     *
     * @var Kunstmaan\TranslatorBundle\Entity\TranslationDomain
     **/
    private $translationDomain;

    public function hasTranslation($locale)
    {

        if (!is_array($this->translations) || count($this->translations) <= 0) {
            return false;
        }

        if ($this->getTranslationByLocale($locale) !== null) {
            return true;
        }

        return false;
    }

    public function getTranslationByLocale($locale)
    {
        if (!is_array($this->translations) || count($this->translations) <= 0) {
            return null;
        }

        foreach ($this->translations as $translation) {
            if($translation->getLocale() == $locale) {
                return $translation;
            }
        }

        return null;
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function setTranslations($translations)
    {
        $this->translations = $translations;
    }

    public function getKeyword()
    {
        return $this->keyword;
    }

    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;
    }

    public function getTranslationDomain()
    {
        return $this->translationDomain;
    }

    public function setTranslationDomain($translationDomain)
    {
        $this->translationDomain = $translationDomain;
    }
}
