<?php

namespace Kunstmaan\NodeBundle\Helper;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\HideFromNodeTreeInterface;
use Kunstmaan\NodeBundle\Entity\HomePageInterface;
use Kunstmaan\NodeSearchBundle\Helper\SearchTypeInterface;
use Kunstmaan\SearchBundle\Helper\IndexableInterface;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

class PagesConfiguration
{
    /** @var array */
    private $configuration;

    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param string $refName
     */
    public function getName($refName)
    {
        return $this->getValue($refName, 'name', substr($refName, strrpos($refName, '\\') + 1));
    }

    /**
     * @param string $refName
     */
    public function getIcon($refName)
    {
        return $this->getValue(
            $refName,
            'icon',
            function ($page) {
                return $page instanceof TreeIconInterface ? $page->getIcon() : '';
            }
        );
    }

    /**
     * @param string $refName
     */
    public function isHiddenFromTree($refName)
    {
        return $this->getValue(
            $refName,
            'hidden_from_tree',
            function ($page) {
                return $page instanceof HideFromNodeTreeInterface;
            }
        );
    }

    /**
     * @param string $refName
     */
    public function isIndexable($refName)
    {
        return $this->getValue(
            $refName,
            'indexable',
            function ($page) {
                /* @var IndexableInterface $page */
                return false === $page instanceof IndexableInterface || $page->isIndexable();
            }
        );
    }

    /**
     * @param string $refName
     */
    public function getSearchType($refName)
    {
        return $this->getValue(
            $refName,
            'search_type',
            function ($page) {
                return $page instanceof SearchTypeInterface ? $page->getSearchType() : ClassLookup::getClass($page);
            }
        );
    }

    /**
     * @param string $refName
     */
    public function isStructureNode($refName)
    {
        return $this->getValue(
            $refName,
            'structure_node',
            function ($page) {
                return $page instanceof HasNodeInterface && $page->isStructureNode();
            }
        );
    }

    /**
     * @param string $refName
     *
     * @return array
     */
    public function getPossibleChildTypes($refName)
    {
        $types = $this->getValue(
            $refName,
            'allowed_children',
            function ($page) {
                return ($page instanceof HasNodeInterface) ? $page->getPossibleChildTypes() : [];
            }
        );

        return array_map(
            function ($type) {
                return $type + ['name' => $this->getName($type['class'])]; // add if not set
            },
            $types
        );
    }

    /**
     * @param string $refName
     */
    public function isHomePage($refName)
    {
        return $this->getValue(
            $refName,
            'is_homepage',
            function ($page) {
                return $page instanceof HomePageInterface;
            }
        );
    }

    /**
     * @return array
     */
    public function getHomepageTypes()
    {
        $pageTypes = array_keys($this->configuration);
        $homePageTypes = [];
        foreach ($pageTypes as $pageType) {
            if ($this->isHomePage($pageType)) {
                $homePageTypes[$pageType] = $this->getName($pageType);
            }
        }

        return $homePageTypes;
    }

    /**
     * @param string         $ref
     * @param string         $name
     * @param callable|mixed $default
     */
    private function getValue($ref, $name, $default = null)
    {
        $refName = \is_object($ref) ? ClassLookup::getClass($ref) : $ref;

        if (isset($this->configuration[$refName][$name])) {
            return $this->configuration[$refName][$name];
        }

        if (false === \is_callable($default)) {
            return $default;
        }

        $page = \is_string($ref) ? new $refName() : $ref;
        $result = $default($page);
        unset($page);

        $this->configuration[$refName][$name] = $result;

        return $result;
    }
}
