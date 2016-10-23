<?php

namespace Kunstmaan\ApiBundle\Model;


use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;

class ApiPage
{
    /** @var string */
    private $type;

    /** @var HasPagePartsInterface */
    private $page;

    /** @var array|ArrayCollection */
    private $pageParts;

    /** @var Node */
    private $node;

    /** @var NodeTranslation */
    private $nodeTranslation;

    /** @var NodeVersion */
    private $nodeVersion;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return HasPagePartsInterface
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param HasPagePartsInterface $page
     */
    public function setPage(HasPagePartsInterface $page)
    {
        $this->page = $page;
        $this->type = get_class($page);
    }

    /**
     * @return array
     */
    public function getPageParts()
    {
        return $this->pageParts;
    }

    /**
     * @param array|ArrayCollection $pageParts
     */
    public function setPageParts($pageParts)
    {
        $this->pageParts = $pageParts;
    }

    /**
     * @return Node
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @param Node $node
     */
    public function setNode(Node $node)
    {
        $this->node = $node;
    }

    /**
     * @return NodeTranslation
     */
    public function getNodeTranslation()
    {
        return $this->nodeTranslation;
    }

    /**
     * @param NodeTranslation $nodeTranslation
     */
    public function setNodeTranslation(NodeTranslation $nodeTranslation)
    {
        $this->nodeTranslation = $nodeTranslation;
    }

    /**
     * @return NodeVersion
     */
    public function getNodeVersion()
    {
        return $this->nodeVersion;
    }

    /**
     * @param NodeVersion $nodeVersion
     */
    public function setNodeVersion(NodeVersion $nodeVersion)
    {
        $this->nodeVersion = $nodeVersion;
    }

}