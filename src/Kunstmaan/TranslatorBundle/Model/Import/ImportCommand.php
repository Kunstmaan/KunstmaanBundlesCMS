<?php

namespace Kunstmaan\TranslatorBundle\Model\Import;

class ImportCommand
{
    private $defaultBundle = false;
    private $bundles = array();
    private $globals = true;
    private $force = false;
    private $locales = false;

    public function getDefaultBundle()
    {
        return $this->defaultBundle;
    }

    public function setDefaultBundle($defaultBundle)
    {
        $this->defaultBundle = $defaultBundle;

        return $this;
    }

    public function getBundles()
    {
        return $this->bundles;
    }

    public function setBundles($bundles)
    {
        $this->bundles = $bundles;

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

    public function getLocales()
    {
        return $this->locales;
    }

    public function setLocales($locales)
    {
        $this->locales = $locales;

        return $this;
    }
}
