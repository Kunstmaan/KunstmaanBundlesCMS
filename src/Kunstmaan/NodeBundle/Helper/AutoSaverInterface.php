<?php

namespace Kunstmaan\NodeBundle\Helper;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Symfony\Component\HttpFoundation\Request;

interface AutoSaverInterface
{
    public function updateAutoSaveFromInput(HasNodeInterface $page, Request $request, Node $node, NodeTranslation $nodeTranslation, ?bool $isStructureNode, NodeVersion $nodeVersion): bool;
}
