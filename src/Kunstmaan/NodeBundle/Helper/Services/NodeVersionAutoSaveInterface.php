<?php

namespace Kunstmaan\NodeBundle\Helper\Services;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Symfony\Component\HttpFoundation\Request;

interface NodeVersionAutoSaveInterface
{
    public function updateAutoSaveFromInput(HasNodeInterface $page, Request $request, Node $node, NodeTranslation $nodeTranslation, ?bool $isStructureNode, NodeVersion $nodeVersion): bool;
}
