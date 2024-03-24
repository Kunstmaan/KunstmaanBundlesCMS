<?php

namespace Kunstmaan\NodeBundle\Event;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;

/**
 * This event will pass metadata when a revert event has been triggered
 */
final class RevertNodeAction extends NodeEvent
{
    /**
     * @var NodeVersion
     */
    public $originNodeVersion;

    /**
     * @var HasNodeInterface
     */
    public $originPage;

    /**
     * @param Node             $node              The node
     * @param NodeTranslation  $nodeTranslation   The nodetranslation
     * @param NodeVersion      $nodeVersion       The node version
     * @param HasNodeInterface $page              The object
     * @param NodeVersion      $originNodeVersion The node version we reverted to
     * @param HasNodeInterface $originPage        The page we reverted to
     */
    public function __construct(Node $node, NodeTranslation $nodeTranslation, NodeVersion $nodeVersion, HasNodeInterface $page, NodeVersion $originNodeVersion, HasNodeInterface $originPage)
    {
        $this->node = $node;
        $this->nodeTranslation = $nodeTranslation;
        $this->nodeVersion = $nodeVersion;
        $this->page = $page;
        $this->originNodeVersion = $originNodeVersion;
        $this->originPage = $originPage;
    }

    /**
     * @param NodeVersion $originNodeVersion
     */
    public function setOriginNodeVersion($originNodeVersion): RevertNodeAction
    {
        $this->originNodeVersion = $originNodeVersion;

        return $this;
    }

    public function getOriginNodeVersion(): NodeVersion
    {
        return $this->originNodeVersion;
    }

    /**
     * @param HasNodeInterface $originPage
     */
    public function setOriginPage($originPage): RevertNodeAction
    {
        $this->originPage = $originPage;

        return $this;
    }

    public function getOriginPage(): HasNodeInterface
    {
        return $this->originPage;
    }
}
