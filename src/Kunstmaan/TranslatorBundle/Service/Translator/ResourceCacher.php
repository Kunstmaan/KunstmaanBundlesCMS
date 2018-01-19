<?php

namespace Kunstmaan\TranslatorBundle\Service\Translator;

use Psr\Log\LoggerInterface;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Finder\Finder;

/**
 * ResourceCacher is used to cache all the resource into a file
 */
class ResourceCacher
{
    /* @var boolean */
    private $debug;

    /* @var string */
    private $cacheDir;

    /** @var LoggerInterface */
    private $logger;

    /**
     * Retrieve resources from cache file (if any)
     *
     * @return mixed false if empty
     */
    public function getCachedResources()
    {
        $resources = false;

        $cache = new ConfigCache($this->getCacheFileLocation(), $this->debug);

        if ($cache->isFresh()) {
            $this->logger->debug('Loading translation resources from cache file.');

            return include $this->getCacheFileLocation();
        }

        return $resources;
    }

    /**
     * Cache an array of resources into the given cache
     *
     * @param  array $resources
     *
     * @return void
     */
    public function cacheResources(array $resources)
    {
        $cache = new ConfigCache($this->getCacheFileLocation(), $this->debug);
        $content = sprintf('<?php return %s;', var_export($resources, true));
        $cache->write($content);
        $this->logger->debug('Writing translation resources to cache file.');
    }

    /**
     * Get cache file location
     *
     * @return string
     */
    public function getCacheFileLocation()
    {
        return sprintf('%s/resources.cached.php', $this->cacheDir);
    }

    /**
     * Remove all cached files (translations/resources)
     *
     * @return bool
     */
    public function flushCache()
    {
        $finder = new Finder();
        $finder->files()->in($this->cacheDir)->name('*.php');

        foreach ($finder as $file) {
            unlink($file->getRealPath());
        }

        return true;
    }

    /**
     * @param bool $debug
     */
    public function setDebug(bool $debug)
    {
        $this->debug = $debug;
    }

    /**
     * @param string $cacheDir
     */
    public function setCacheDir(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
