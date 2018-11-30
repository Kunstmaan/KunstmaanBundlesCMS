<?php

namespace Kunstmaan\TranslatorBundle\Service\Command;

abstract class AbstractCommandHandler
{
    /**
     * Managed locales from config file
     *
     * @var array
     */
    protected $managedLocales;

    /**
     * Kernel
     *
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
     *
     * @param string $locales ex. nl,fr, de, SE, eN
     *
     * @return array
     *
     * @throws \Exception If the string with locales can't be parsed
     */
    public function parseRequestedLocales($locales)
    {
        return $this->parseCommaSeperatedValuesToArray($locales);
    }

    public function parseRequestedDomains($domains)
    {
        return $this->parseCommaSeperatedValuesToArray($domains);
    }

    public function parseCommaSeperatedValuesToArray($values)
    {
        if (!is_array($values) && strpos($values, ',') === false && mb_strlen(trim($values)) == 2) {
            return array(strtolower(trim($values)));
        }

        if (!is_array($values)) {
            $values = explode(',', $values);
        }

        if (count($values) >= 1) {
            return array_map(function ($value) {
                return strtolower(trim($value));
            }, $values);
        }

        throw new \Exception('Invalid values specified');
    }
}
