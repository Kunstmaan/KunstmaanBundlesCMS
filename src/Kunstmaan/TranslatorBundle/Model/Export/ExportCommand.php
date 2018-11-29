<?php

namespace Kunstmaan\TranslatorBundle\Model\Export;

class ExportCommand
{
    private $domains = false;

    private $format = 'yml';

    private $locales = false;

    public function getDomains()
    {
        return $this->domains;
    }

    public function setDomains($domains)
    {
        $this->domains = $domains;

        return $this;
    }

    public function getLocales()
    {
        return $this->locales;
    }

    public function setLocales($locales)
    {
        $this->locales = $locales;

        return $this;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }
}
