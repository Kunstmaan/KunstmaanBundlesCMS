<?php

namespace Kunstmaan\NodeBundle\Event;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;

/**
 * This event will pass metadata when a revert event has been triggered
 */
class RevertNodeAction extends NodeEvent
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
     *
     * @return RevertNodeAction
     */
    public function setOriginNodeVersion($originNodeVersion)
    {
        $this->originNodeVersion = $originNodeVersion;

        return $this;
    }

    /**
     * @return NodeVersion
     */
    public function getOriginNodeVersion()
    {
        return $this->originNodeVersion;
    }

    /**
     * @param HasNodeInterface $originPage
     *
     * @return RevertNodeAction
     */
    public function setOriginPage($originPage)
    {
        $this->originPage = $originPage;

        return $this;
    }

    /**
     * @return HasNodeInterface
     */
    public function getOriginPage()
    {
        return $this->originPage;
    }
}
