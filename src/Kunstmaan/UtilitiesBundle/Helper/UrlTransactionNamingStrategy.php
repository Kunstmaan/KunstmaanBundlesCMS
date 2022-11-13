<?php

namespace Kunstmaan\UtilitiesBundle\Helper;

use Ekino\NewRelicBundle\TransactionNamingStrategy\TransactionNamingStrategyInterface;
use Symfony\Component\HttpFoundation\Request;

trigger_deprecation('kunstmaan/utilities-bundle', '6.3', 'The "%s" is deprecated and will be removed in 7.0.', UrlTransactionNamingStrategy::class);

/**
 * NEXT_MAJOR: Also remove newrelic dependency from composer, suggest from utilities bundle composer and from cms-skeleton package depenencies.
 */
class UrlTransactionNamingStrategy implements TransactionNamingStrategyInterface
{
    public function getTransactionName(Request $request): string
    {
        return $request->getPathInfo();
    }
}
