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
        return (PHP_MAJOR_VERSION == 7 && !class_exists('\Elastica\Tool\CrossIndex'));
    }
}