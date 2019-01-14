<?php

namespace Kunstmaan\NodeBundle\Helper;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\HideFromNodeTreeInterface;
use Kunstmaan\NodeBundle\Entity\HomePageInterface;
use Kunstmaan\NodeSearchBundle\Helper\SearchTypeInterface;
use Kunstmaan\SearchBundle\Helper\IndexableInterface;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

/**
 * Class PagesConfiguration
 */
class PagesConfiguration
{
    /** @var array */
    private $configuration;

    /**
     * PagesConfiguration constructor.
     *
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param string $refName
     *
     * @return mixed
     */
    public function getName($refName)
    {
        return $this->getValue($refName, 'name', substr($refName, strrpos($refName, '\\') + 1));
    }

    /**
     * @param string $refName
     *
     * @return mixed
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
     *
     * @return mixed
     */
    public function isHiddenFromTree($refName)
    {
        return $this->getValue(
            $refName,
            'hidden_from_tree',
            function ($page) {
                /* @noinspection PhpDeprecationInspection */
                return $page instanceof HideFromNodeTreeInterface;
            }
        );
    }

    /**
     * @param string $refName
     *
     * @return mixed
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
     *
     * @return mixed
     */
    public function getSearchType($refName)
    {
        return $this->getValue(
            $refName,
            'search_type',
            function ($page) {
                /* @noinspection PhpDeprecationInspection */
                return $page instanceof SearchTypeInterface ? $page->getSearchType() : ClassLookup::getClass($page);
            }
        );
    }

    /**
     * @param string $refName
     *
     * @return mixed
     */
    public function isStructureNode($refName)
    {
        return $this->getValue(
            $refName,
            'structure_node',
            function ($page) {
                /* @noinspection PhpDeprecationInspection */
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
                /* @noinspection PhpDeprecationInspection */
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
     *
     * @return mixed
     */
    public function isHomePage($refName)
    {
        return $this->getValue(
            $refName,
            'is_homepage',
            function ($page) {
                /* @noinspection PhpDeprecationInspection */
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
     *
     * @return mixed
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
