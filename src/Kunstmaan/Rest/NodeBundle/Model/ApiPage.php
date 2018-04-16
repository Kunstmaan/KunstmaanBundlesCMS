<?php

/*
 * This file is part of the KunstmaanBundlesCMS package.
 *
 * (c) Kunstmaan <https://github.com/Kunstmaan/KunstmaanBundlesCMS/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kunstmaan\Rest\NodeBundle\Model;

use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

/**
 * Class ApiPage
 */
class ApiPage
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var EntityInterface
     */
    private $page;

    /**
     * @var ApiPageTemplate
     */
    private $pageTemplate;

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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return EntityInterface
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param EntityInterface $page
     *
     * @return $this
     */
    public function setPage(EntityInterface $page)
    {
        $this->page = $page;
        $this->type = ClassLookup::getClass($page);

        return $this;
    }

    /**
     * @return ApiPageTemplate
     */
    public function getPageTemplate()
    {
        return $this->pageTemplate;
    }

    /**
     * @param ApiPageTemplate $pageTemplate
     */
    public function setPageTemplate(ApiPageTemplate $pageTemplate)
    {
        $this->pageTemplate = $pageTemplate;
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
     *
     * @return $this
     */
    public function setNode(Node $node)
    {
        $this->node = $node;

        return $this;
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
     *
     * @return $this
     */
    public function setNodeTranslation(NodeTranslation $nodeTranslation)
    {
        $this->nodeTranslation = $nodeTranslation;

        return $this;
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
     *
     * @return $this
     */
    public function setNodeVersion(NodeVersion $nodeVersion)
    {
        $this->nodeVersion = $nodeVersion;

        return $this;
    }
}
