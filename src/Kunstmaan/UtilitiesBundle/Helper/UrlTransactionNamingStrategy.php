<?php

namespace Kunstmaan\UtilitiesBundle\Helper;

use Ekino\Bundle\NewRelicBundle\TransactionNamingStrategy\TransactionNamingStrategyInterface;
use Symfony\Component\HttpFoundation\Request;

class UrlTransactionNamingStrategy implements TransactionNamingStrategyInterface
{
    public function getTransactionName(Request $request)
    {
        return $request->getPathInfo();
    }
}
