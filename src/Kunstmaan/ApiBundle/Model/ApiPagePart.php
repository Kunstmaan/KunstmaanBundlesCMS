<?php
/**
 * Created by PhpStorm.
 * User: ruud
 * Date: 23/10/2016
 * Time: 19:54
 */

namespace Kunstmaan\ApiBundle\Model;


use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;

class ApiPagePart
{
    /** @var string */
    private $type;

    /** @var AbstractPagePart */
    private $pagePart;

    /** @var string */
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
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return AbstractPagePart
     */
    public function getPagePart()
    {
        return $this->pagePart;
    }

    /**
     * @param AbstractPagePart $pagePart
     */
    public function setPagePart(AbstractPagePart $pagePart)
    {
        $this->pagePart = $pagePart;
        $this->type = get_class($pagePart);
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
     */
    public function setContext($context)
    {
        $this->context = $context;
    }
}