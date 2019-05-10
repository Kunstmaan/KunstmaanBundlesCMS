<?php

namespace Kunstmaan\NodeBundle\Entity;

use Kunstmaan\NodeBundle\ValueObject\PageTab;

interface PageTabInterface
{
    /**
     * @return PageTab[]
     */
    public function getTabs();
}
