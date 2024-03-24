<?php

namespace Kunstmaan\NodeBundle\Event;

use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\TabPane;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * The event to pass metadata if the adaptForm event is triggered
 */
final class AdaptFormEvent extends Event
{
    /**
     * @var TabPane
     */
    private $tabPane;

    private $page;

    /**
     * @var Node
     */
    private $node;

    /**
     * @var NodeTranslation
     */
    private $nodeTranslation;

    /**
     * @var NodeVersion
     */
    private $nodeVersion;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param TabPane          $tabPane         The tab pane
     * @param HasNodeInterface $page            The page
     * @param Node             $node            The node
     * @param NodeTranslation  $nodeTranslation The node translation
     * @param NodeVersion      $nodeVersion     The node version
     */
    public function __construct(Request $request, TabPane $tabPane, $page = null, ?Node $node = null, ?NodeTranslation $nodeTranslation = null, ?NodeVersion $nodeVersion = null)
    {
        $this->request = $request;
        $this->tabPane = $tabPane;
        $this->page = $page;
        $this->node = $node;
        $this->nodeTranslation = $nodeTranslation;
        $this->nodeVersion = $nodeVersion;
    }

    public function getNode(): Node
    {
        return $this->node;
    }

    public function getNodeTranslation(): NodeTranslation
    {
        return $this->nodeTranslation;
    }

    public function getNodeVersion(): NodeVersion
    {
        return $this->nodeVersion;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getTabPane(): TabPane
    {
        return $this->tabPane;
    }

    public function getRequest()
    {
        return $this->request;
    }
}
