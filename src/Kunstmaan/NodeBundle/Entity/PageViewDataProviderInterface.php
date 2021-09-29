<?php

namespace Kunstmaan\NodeBundle\Entity;

use Kunstmaan\NodeBundle\Helper\RenderContext;

interface PageViewDataProviderInterface
{
    /**
     * In this function you can add extra view variables to the `$renderContext` object. If the request should be
     * redirected add a response via the `$renderContext->setResponse()` method.
     */
    public function provideViewData(NodeTranslation $nodeTranslation, RenderContext $renderContext): void;
}
