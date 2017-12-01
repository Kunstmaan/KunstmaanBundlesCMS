<?php

namespace Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL;

use Kunstmaan\AdminListBundle\AdminList\FilterType\Traits\BooleanFilterTrait;

/**
 * BooleanFilterType
 */
class BooleanFilterType extends AbstractDBALFilterType
{
    use BooleanFilterTrait;
}
