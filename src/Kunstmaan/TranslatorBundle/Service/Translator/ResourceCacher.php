<?php

namespace Kunstmaan\TranslatorBundle\Service\Translator;

use Symfony\Component\Config\ConfigCache;

class ResourceCacher
{
    private $debug;
    private $cacheDir;
    private $logger;


    public function getCachedResources($forceRefresh = false)
    {
        $resources = false;

        $cache = new ConfigCache($this->getCacheFileLocation(), $this->debug);

        if($cache->isFresh()) {
            $this->logger->debug('Loading translation resources from cache file.');
            return include $this->getCacheFileLocation();
        }

        return $resources;
    }

    public function cacheResources(array $resources)
    {
        $cache = new ConfigCache($this->getCacheFileLocation(), $this->debug);
        $content = sprintf('<?php return %s;', var_export($resources, true));
        // TODO write metadate for this cache?
        $cache->write($content);
        $this->logger->debug('Writing translation resources to cache file.');
    }

    public function getCacheFileLocation()
    {
        return sprintf('%s/resources.cached.php', $this->cacheDir);
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
