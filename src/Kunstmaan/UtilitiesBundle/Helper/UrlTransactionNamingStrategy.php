<?php

namespace Kunstmaan\UtilitiesBundle\Helper;

use Ekino\NewRelicBundle\TransactionNamingStrategy\TransactionNamingStrategyInterface;
use Symfony\Component\HttpFoundation\Request;

class UrlTransactionNamingStrategy implements TransactionNamingStrategyInterface
{
    public function getTransactionName(Request $request): string
    {
        return $request->getPathInfo();
    }
}
