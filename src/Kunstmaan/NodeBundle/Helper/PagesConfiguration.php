<?php

namespace Kunstmaan\NodeBundle\Helper;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Mapping\ClassMetadata;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use /** @noinspection PhpDeprecationInspection */
    Kunstmaan\NodeBundle\Entity\HideFromNodeTreeInterface;
use Kunstmaan\NodeBundle\Entity\HomePageInterface;
use /** @noinspection PhpDeprecationInspection */
    Kunstmaan\NodeSearchBundle\Helper\SearchTypeInterface;
use Kunstmaan\SearchBundle\Helper\IndexableInterface;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;


class PagesConfiguration
{
    private $configuration;

    /**
     * @var Registry
     */
    private $doctrine;

    public function __construct($configuration)
    {
        $this->configuration = $configuration;

    }

    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Collects all entities that implement HomePageInterface and loads them into configuration
     */
    public function initConfiguration()
    {
        $metas = $this->doctrine->getManager()->getMetadataFactory()->getAllMetadata();
        foreach ($metas as $meta) {
            /** @var ClassMetadata $meta */
            if ($meta->getReflectionClass()->implementsInterface(HomePageInterface::class)) {
                $refName = $meta->getName();
                if (!isset($this->configuration[$refName])) {
                    $this->configuration[$refName] = [
                        // name is created from classname
                        'name' => substr($refName, strrpos($refName, '\\') + 1),
                    ];
                }
            }
        }
    }

    public function getName($refName)
    {
        return $this->getValue($refName, 'name', substr($refName, strrpos($refName, '\\') + 1));
    }

    public function getIcon($refName)
    {
        return $this->getValue($refName, 'icon');
    }

    public function isHiddenFromTree($refName)
    {
        return $this->getValue($refName, 'hidden_from_tree', function ($page) {
            /** @noinspection PhpDeprecationInspection */
            return $page instanceof HideFromNodeTreeInterface;
        });
    }

    public function isIndexable($refName)
    {
        return $this->getValue($refName, 'indexable', function ($page) {
            /** @var IndexableInterface $page */
            return false === $page instanceof IndexableInterface || $page->isIndexable();
        });
    }

    public function getSearchType($refName)
    {
        return $this->getValue($refName, 'search_type', function ($page) {
            /** @noinspection PhpDeprecationInspection */
            return $page instanceof SearchTypeInterface ? $page->getSearchType() : ClassLookup::getClass($page);
        });
    }

    public function isStructureNode($refName)
    {
        return $this->getValue($refName, 'structure_node', function ($page) {
            /** @noinspection PhpDeprecationInspection */
            return $page instanceof HasNodeInterface && $page->isStructureNode();
        });
    }

    public function getPossibleChildTypes($refName)
    {
        $types = $this->getValue($refName, 'allowed_children', function ($page) {
            /** @noinspection PhpDeprecationInspection */
            return ($page instanceof HasNodeInterface) ? $page->getPossibleChildTypes() : [];
        });

        return array_map(function ($type) {
            return $type + ['name' => $this->getName($type['class'])]; // add if not set
        }, $types);
    }

    public function isHomePage($refName)
    {
        return $this->getValue($refName, 'is_homepage', function ($page) {
            /** @noinspection PhpDeprecationInspection */
            return $page instanceof HomePageInterface;
        });
    }

    public function getHomepageTypes()
    {
        $pageTypes = array_keys($this->configuration);
        $homePageTypes = array();
        foreach ($pageTypes as $pageType) {
            if ($this->isHomePage($pageType)) {
                $homePageTypes[$pageType] = $this->getName($pageType);
            }
        }

        return $homePageTypes;
    }

    /**
     * @param string $ref
     * @param string $name
     * @param Callable|mixed $default
     *
     * @return mixed
     */
    private function getValue($ref, $name, $default = null)
    {
        $refName = is_object($ref) ? ClassLookup::getClass($ref) : $ref;

        if (isset($this->configuration[$refName][$name])) {
            return $this->configuration[$refName][$name];
        }

        if (false === is_callable($default)) {
            return $default;
        }

        $page = is_string($ref) ? new $refName : $ref;
        $result = $default($page);
        unset($page);

        $this->configuration[$refName][$name] = $result;

        return $result;
    }
}
