<?php

namespace Kunstmaan\TranslatorBundle\Model\Translation;

/**
 * Defines a new translation - bridge between controller and service layer
 */
class NewTranslation
{
    /**
     * An array with all translations, key = locale, value = translation
     *
     * @var array
     */
    protected $locales = array();

    /**
     * Keyword of the new translations
     *
     * @var string
     */
    protected $keyword;

    /**
     * Domain name of the new translations
     *
     * @var string
     */
    protected $domain;

    public function getLocales()
    {
        return $this->locales;
    }

    public function setLocales($locales)
    {
        $this->locales = $locales;
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
