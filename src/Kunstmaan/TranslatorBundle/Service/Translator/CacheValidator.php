<?php

namespace Kunstmaan\TranslatorBundle\Service\Translator;

use Symfony\Component\Finder\Finder;

/**
 * Validates and checks translations cache
 */
class CacheValidator
{
    /**
     * @var boolean
     */
    private $debug;

    /**
     * Where to store cache files
     * @var string
     */
    private $cacheDir;

    private $translationRepository;

    /**
     * Checks the caching files of they are even with the stasher content
     * @return boolean
     */
    public function isCacheFresh()
    {
        $fileDate = $this->getOldestCachefileDate();
        $stashDate = $this->getLastTranslationChangeDate();

        if ($fileDate === null) {
            return true;
        }

        return $fileDate >= $stashDate;
    }

    /**
     * Get the last updated or inserted from all database translations
     * @return DateTime last createdAt or updateAt date from the translations stash
     */
    public function getLastTranslationChangeDate()
    {
        return $this->translationRepository->getLastChangedTranslationDate();
    }

    /**
     * Retrieve a Datetime of the oldest cache file made
     * @return DateTime mtime of oldest cache file
     */
    public function getOldestCachefileDate()
    {
        if (!is_dir($this->cacheDir)) {
            return null;
        }

        $finder = new Finder();
        $finder->files()
            ->name('catalogue*.php')
            ->sortByModifiedTime()
            ->in($this->cacheDir);

        foreach ($finder as $file) {
            $date = new \DateTime();
            $date->setTimestamp($file->getMTime());

            return $date;
        }

        return null;
    }

    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function setTranslationRepository($translationRepository)
    {
        $this->translationRepository = $translationRepository;
    }

}
