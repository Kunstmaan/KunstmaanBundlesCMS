<?php

namespace Kunstmaan\TranslatorBundle\Service;

abstract class AbstractCommandHandler
{
    /**
     * Managed locales from config file
     * @var array
     */
    protected $managedLocales;

    /**
     * Kernel
     * @var AppKernel
     */
    protected $kernel;

    public function setManagedLocales($managedLocales)
    {
        $this->managedLocales = $managedLocales;
    }

    public function setKernel($kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Parses a string of locales into an array
     * @param  string     $locales ex. nl,fr, de, SE, eN
     * @return array
     * @throws \Exception If the string with locales can't be parsed
     */
    public function parseRequestedLocales($locales)
    {
        if (!is_array($locales) && strpos($locales, ',') === false && mb_strlen(trim($locales)) == 2) {
            return array(strtolower(trim($locales)));
        }

        if (!is_array($locales)) {
            $locales = explode(',', $locales);
        }

        if (count($locales) >= 1) {
            return array_map(function($locale) { return strtolower(trim($locale)); }, $locales);
        }

        throw new \Exception('Invalid locales specified');
    }
}