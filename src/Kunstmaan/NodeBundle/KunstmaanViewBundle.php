<?php

namespace Kunstmaan\ViewBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * This bundle contains utils for rendering pages from slugs
 */
class KunstmaanViewBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return "TwigBundle";
    }
}
