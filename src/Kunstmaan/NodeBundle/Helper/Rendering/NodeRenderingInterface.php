<?php

namespace Kunstmaan\NodeBundle\Helper\Rendering;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Symfony\Component\HttpFoundation\Request;

interface NodeRenderingInterface
{
    public function render(string $locale, Node $node, NodeTranslation $nodeTranslation, HasNodeInterface $page, Request $request);
}
