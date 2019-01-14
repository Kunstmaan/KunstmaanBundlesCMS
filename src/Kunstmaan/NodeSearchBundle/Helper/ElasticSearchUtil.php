<?php

namespace Kunstmaan\NodeSearchBundle\Helper;

use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\NoNodesAvailableException;

/**
 * Class ElasticSearchUtil
 */
final class ElasticSearchUtil
{
    /** @var array */
    private static $esClientInfo;

    /**
     * @var array
     *
     * @return bool
     */
    public static function useVersion6($hosts = array())
    {
        if (PHP_MAJOR_VERSION < 7) {
            return false;
        }

        $info = self::getESVersionInfo($hosts);

        if (null !== $info) {
            $versionParts = explode('.', $info['version']['number']);
            $majorVersion = $versionParts[0];

            return $majorVersion > 2;
        }

        return false;
    }

    /**
     * @var array
     *
     * @return array
     */
    private static function getESVersionInfo($hosts)
    {
        try {
            if (null === self::$esClientInfo) {
                $client = ClientBuilder::create()->setHosts($hosts)->build();
                self::$esClientInfo = $client->info();
            }
        } catch (NoNodesAvailableException $e) {
            self::$esClientInfo = null;
        }

        return self::$esClientInfo;
    }
}
