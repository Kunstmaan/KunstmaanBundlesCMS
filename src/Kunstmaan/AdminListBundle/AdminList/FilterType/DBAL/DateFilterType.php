<?php

namespace Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL;

use Kunstmaan\AdminListBundle\AdminList\FilterType\Traits\DateFilterTrait;

/**
 * DateFilterType
 */
class DateFilterType extends AbstractDBALFilterType
{
    use DateFilterTrait;
}
