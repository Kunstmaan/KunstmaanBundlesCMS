<?php

namespace Kunstmaan\AdminBundle\Tests\Mocks;

use Doctrine\DBAL\Driver\Statement;

/**
 * StatementMock.
 */
abstract class StatementMock implements \Iterator, Statement
{
}
