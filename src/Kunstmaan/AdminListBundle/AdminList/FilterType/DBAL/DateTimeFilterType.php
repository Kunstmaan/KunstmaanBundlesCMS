<?php

namespace Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL;

use Kunstmaan\AdminListBundle\AdminList\FilterType\Traits\DateTimeFilterTrait;

/**
 * DateTimeFilterType
 */
class DateTimeFilterType extends AbstractDBALFilterType
{
    use DateTimeFilterTrait;
}
