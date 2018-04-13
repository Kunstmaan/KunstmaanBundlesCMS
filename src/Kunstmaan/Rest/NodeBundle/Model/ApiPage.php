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

use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Class ApiPage
 *
 * @author Ruud Denivel <ruud.denivel@kunstmaan.be>
 *
 * @SWG\Definition()
 */
class ApiPage
{
    /**
     * @var string
     * @SWG\Property(type="string", example="Kunstmaan\SomeBundle\Entity\Pages\HomePage")
     */
    private $type;

    /**
     * @var HasPagePartsInterface
     * @SWG\Property(type="object")
     */
    private $page;

    /**
     * @var ApiPagePart[]
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(
     *      allOf={
     *         @Model(type=ApiPagePart::class)
     *      }
     *    )
     * )
     */
    private $pageParts;

    /**
     * @var Node
     * @SWG\Property(ref="#/definitions/node")
     */
    private $node;

    /**
     * @var NodeTranslation
     * @SWG\Property(ref="#/definitions/nodeTranslation")
     */
    private $nodeTranslation;

    /**
     * @var NodeVersion
     * @SWG\Property(ref="#/definitions/nodeVersion")
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
     * @return HasPagePartsInterface
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param HasPagePartsInterface $page
     *
     * @return $this
     */
    public function setPage(HasPagePartsInterface $page)
    {
        $this->page = $page;
        $this->type = ClassLookup::getClass($page);

        return $this;
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
     *
     * @return $this
     */
    public function setPageParts($pageParts)
    {
        $this->pageParts = $pageParts;

        return $this;
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
