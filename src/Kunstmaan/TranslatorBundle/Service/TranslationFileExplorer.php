<?php

namespace Kunstmaan\TranslatorBundle\Service;

use Symfony\Component\Finder\Finder;

class TranslationFileExplorer
{

    private $defaultTranslationFolder = 'Resources/translations';
    private $fileFormats;

    /**
     * Looks in the path for Resources/translation files and returns a finder object with the result
     */
    public function find($path, array $locales)
    {
        $finder = null;

        $exploreDir = $path.'/'.$this->defaultTranslationFolder;

        if (is_dir($exploreDir)) {

            $finder = new Finder();
            $finder->files()
                ->name(sprintf('/(.*(%s)\.(%s))/', implode('|', $locales), implode('|', $this->fileFormats)))
                ->in($exploreDir);
        }

        return $finder;
    }

    public function setFileFormats($fileFormats)
    {
        $this->fileFormats = $fileFormats;
    }
}
