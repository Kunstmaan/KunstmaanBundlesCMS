<?php

namespace Kunstmaan\TranslatorBundle\Service;

use Kunstmaan\TranslatorBundle\Service\Exception\TranslationsNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Kernel;

class TranslationFileExplorer
{
    /**
     * An array of supported file formats to look for
     *
     * @var array
     */
    private $fileFormats = array();

    /**
     *  Looks in the path for Resources/translation files and returns a finder object with the result
     *
     * @param string $path
     * @param array  $locales
     *
     * @return \Symfony\Component\Finder\Finder
     */
    public function find($path, array $locales, $translationDirectory = null)
    {
        $finder = new Finder();
        $translationDirectory = $translationDirectory ?? $this->getDefaultTranslationFolder();

        $exploreDir = $path . '/' . $translationDirectory;

        if (is_dir($exploreDir)) {
            $finder->files()
                ->name(sprintf('/(.*(%s)\.(%s))/', implode('|', $locales), implode('|', $this->fileFormats)))
                ->in($exploreDir);

            return $finder;
        }

        throw new TranslationsNotFoundException('Directory `' . $exploreDir . '` does not exist, translations could not be found.');
    }

    public function setFileFormats($fileFormats)
    {
        $this->fileFormats = $fileFormats;
    }

    protected function getDefaultTranslationFolder(): string
    {
        if (Kernel::VERSION_ID >= 40000) {
            return 'translations';
        }

        return 'Resources/translations';
    }
}
