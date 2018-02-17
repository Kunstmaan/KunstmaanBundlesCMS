<?php

declare(strict_types=1);

namespace Kunstmaan\UtilitiesBundle\Helper;

use Ekino\Bundle\NewRelicBundle\TransactionNamingStrategy\TransactionNamingStrategyInterface;
use Symfony\Component\HttpFoundation\Request;

class UrlTransactionNamingStrategy implements TransactionNamingStrategyInterface
{
    public function getTransactionName(Request $request): string
    {
        return $request->getPathInfo();
    }

}
