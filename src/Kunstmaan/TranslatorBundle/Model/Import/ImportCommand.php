<?php

namespace Kunstmaan\TranslatorBundle\Model\Import;

class ImportCommand
{
    private $bundle = false;
    private $globals = true;
    private $force = false;
    private $locale = false;

    public function getBundle()
    {
        return $this->bundle;
    }

    public function setBundle($bundle)
    {
        $this->bundle = $bundle;

        return $this;
    }

    public function getGlobals()
    {
        return $this->globals;
    }

    public function setGlobals($globals)
    {
        $this->globals = $globals;

        return $this;
    }

    public function getForce()
    {
        return $this->force;
    }

    public function setForce($force)
    {
        $this->force = $force;

        return $this;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }
}