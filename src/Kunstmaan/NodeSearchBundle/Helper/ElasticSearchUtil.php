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
     * @return bool
     */
    public static function useVersion6()
    {
        if (PHP_MAJOR_VERSION < 7 || !class_exists('\Elastica\Tool\CrossIndex')) {
            return false;
        }

        $info = self::getESVersionInfo();

        if (null !== $info) {
            $versionParts = explode('.', $info['version']['number']);
            $majorVersion = $versionParts[0];

            return ($majorVersion > 2);
        }

        return false;
    }

    /**
     * @return array
     */
    private static function getESVersionInfo()
    {
        try {
            if (null === self::$esClientInfo) {
                $client = ClientBuilder::create()->build();
                self::$esClientInfo = $client->info();
            }
        } catch (NoNodesAvailableException $e) {
            self::$esClientInfo = null;
        }

        return self::$esClientInfo;
    }
}
