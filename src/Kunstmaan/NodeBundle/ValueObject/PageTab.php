<?php

namespace Kunstmaan\NodeBundle\ValueObject;

class PageTab
{
    /**
     * @var string
     */
    private $internalName;

    /**
     * @var string
     */
    private $tabTitle;

    /**
     * @var string
     */
    private $formTypeClass;

    /**
     * @var int|null
     */
    private $position;

    /**
     * @param string   $internalName
     * @param string   $tabTitle
     * @param string   $formTypeClass
     * @param int|null $position
     */
    public function __construct($internalName, $tabTitle, $formTypeClass, $position = null)
    {
        $this->internalName = $internalName;
        $this->tabTitle = $tabTitle;
        $this->formTypeClass = $formTypeClass;
        $this->position = $position;
    }

    /**
     * @return string
     */
    public function getInternalName()
    {
        return $this->internalName;
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->tabTitle;
    }

    /**
     * @return string
     */
    public function getFormTypeClass()
    {
        return $this->formTypeClass;
    }

    /**
     * @return int|null
     */
    public function getPosition()
    {
        return $this->position;
    }
}
