<?php

namespace Kunstmaan\AdminBundle\Helper\Menu;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * MenuBuilder
 */
class MenuBuilder
{
    /* @var TranslatorInterface $translator */
    private $translator;
    /* @var MenuAdaptorInterface[] $adaptors */
    private $adaptors = array();
    private $topmenuitems = null;
    /* @var ContainerInterface $container */
    private $container;

    private $currentCache = null;

    /**
     * @param TranslatorInterface $translator The translator
     * @param ContainerInterface  $container  The container
     */
    public function __construct(TranslatorInterface $translator, ContainerInterface $container)
    {
        $this->translator = $translator;
        $this->container  = $container;
    }

    /**
     * @param MenuAdaptorInterface $adaptor
     */
    public function addAdaptMenu(MenuAdaptorInterface $adaptor)
    {
        $this->adaptors[] = $adaptor;
    }

    /**
     * @return MenuItem|null
     */
    public function getCurrent()
    {
        if ($this->currentCache !== null) {
            return $this->currentCache;
        }
        $active = null;
        do {
            /* @var MenuItem[] $children */
            $children         = $this->getChildren($active);
            $foundActiveChild = false;
            foreach ($children as $child) {
                if ($child->getActive()) {
                    $foundActiveChild = true;
                    $active           = $child;
                    break;
                }
            }
        } while ($foundActiveChild);
        $this->currentCache = $active;

        return $active;
    }

    /**
     * @return MenuItem[]
     */
    public function getBreadCrumb()
    {
        $result  = array();
        $current = $this->getCurrent();
        while (!is_null($current)) {
            array_unshift($result, $current);
            $current = $current->getParent();
        }

        return $result;
    }

    /**
     * @return TopMenuItem|null
     */
    public function getLowestTopChild()
    {
        $current = $this->getCurrent();
        while (!is_null($current)) {
            if ($current instanceof TopMenuItem) {
                return $current;
            }
            $current = $current->getParent();
        }

        return null;
    }

    /**
     * @return MenuItem[]
     */
    public function getTopChildren()
    {
        if (is_null($this->topmenuitems)) {
            $request = $this->container->get('request');
            $this->topmenuitems = array();
            foreach ($this->adaptors as $menuadaptor) {
                $menuadaptor->adaptChildren($this, $this->topmenuitems, null, $request);
            }
        }

        return $this->topmenuitems;
    }

    /**
     * @param MenuItem $parent
     *
     * @return MenuItem[]
     */
    public function getChildren(MenuItem $parent = null)
    {
        if ($parent == null) {
            return $this->getTopChildren();
        }
        $request = $this->container->get('request');
        $result = array();
        foreach ($this->adaptors as $menuadaptor) {
            $menuadaptor->adaptChildren($this, $result, $parent, $request);
        }

        return $result;
    }

}
