<?php

namespace Kunstmaan\TranslatorBundle\Service\Loader;

interface LoaderInterface
{
    public function load($path, $locale, $domain);
}
