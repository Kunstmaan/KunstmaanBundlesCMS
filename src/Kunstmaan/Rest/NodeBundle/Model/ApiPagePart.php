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

use Kunstmaan\PagePartBundle\Helper\PagePartInterface;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Swagger\Annotations as SWG;

/**
 * Class ApiPagePart
 * @author Ruud Denivel <ruud.denivel@kunstmaan.be>
 *
 * @SWG\Definition()
 */
class ApiPagePart
{
    /**
     * @var string
     * @SWG\Property()
     */
    private $type;

    /**
     * @var PagePartInterface
     * @SWG\Property(
     *     type="object"
     * )
     */
    private $pagePart;

    /**
     * @var string
     * @SWG\Property()
     */
    private $context;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return PagePartInterface
     */
    public function getPagePart()
    {
        return $this->pagePart;
    }

    /**
     * @param PagePartInterface $pagePart
     * @return $this
     */
    public function setPagePart(PagePartInterface $pagePart)
    {
        $this->pagePart = $pagePart;
        $this->type = ClassLookup::getClass($pagePart);

        return $this;
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param string $context
     * @return $this
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }
}