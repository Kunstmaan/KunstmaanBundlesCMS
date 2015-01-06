<?php

namespace Kunstmaan\TranslatorBundle\Service\Translator;

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Process\Exception\RuntimeException;

/**
 * ResourceCacher is used to cache all the resource into a file
 */
class ResourceCacher
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

    /**
     * Logger
     * @var \Symfony\Component\HttpKernel\Log\LoggerInterface
     */
    private $logger;

    /**
     * Retrieve resources from cache file (if any)
     * @return resources false if empty
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
     * @param  array $resources
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
     * @return string
     */
    public function getCacheFileLocation()
    {
        return sprintf('%s/resources.cached.php', $this->cacheDir);
    }

    /**
     * Remove all cached files (translations/resources)
     * @return void
     */
    public function flushCache()
    {
        $command = sprintf('rm -f %s/*.php', $this->cacheDir);
        exec($command, $ouput, $return);

        if ((string) $return != '0') {
            throw new RuntimeException('Flushing translation cache failed');
        }

        return true;
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
}
