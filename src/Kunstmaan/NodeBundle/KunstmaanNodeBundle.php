<?php

namespace Kunstmaan\NodeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * KunstmaanNodeBundle
 */
class KunstmaanNodeBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return "TwigBundle";
    }
}
