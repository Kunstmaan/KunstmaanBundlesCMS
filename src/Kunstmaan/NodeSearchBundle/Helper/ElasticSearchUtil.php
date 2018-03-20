<?php

namespace Kunstmaan\NodeSearchBundle\Helper;

/**
 * Class ElasticSearchUtil
 */
final class ElasticSearchUtil
{
    /**
     * @return bool
     */
    public static function useVersion6()
    {
        return (PHP_VERSION[0] == 7 && !class_exists('\Elastica\Tool\CrossIndex'));
    }
}