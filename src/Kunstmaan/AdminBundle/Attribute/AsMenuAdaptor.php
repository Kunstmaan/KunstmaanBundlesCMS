<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kunstmaan\AdminBundle\Attribute;

/**
 * Service tag to autoconfigure menu adaptors.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class AsMenuAdaptor
{
    public function __construct(
        public int $priority = 0,
    ) {
    }
}
