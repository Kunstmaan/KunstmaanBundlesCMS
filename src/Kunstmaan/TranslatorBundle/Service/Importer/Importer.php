<?php
namespace Kunstmaan\TranslatorBundle\Service\Importer;

class Importer
{

    private $loaders = array();

    public function import(\Symfony\Component\Finder\SplFileInfo $file, $force = false)
    {
        $this->validateLoaders($this->loaders);

        list($domain, $locale, $extension) = explode('.', $file->getFilename());

        $loader = $this->loaders[$extension];
        var_dump($loader);
    }

    public function validateLoaders($loaders = array())
    {
        if(!is_array($loaders) || count($loaders) <= 0) {
            throw new \Exception('No translation file loaders tagged.');
        }
    }

    public function setLoaders(array $loaders)
    {
        $this->loaders = $loaders;
    }
}