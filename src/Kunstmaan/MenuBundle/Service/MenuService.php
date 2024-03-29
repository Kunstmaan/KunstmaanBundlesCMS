<?php

namespace Kunstmaan\MenuBundle\Service;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;

class MenuService
{
    /**
     * @var array
     */
    private $menuNames;

    /**
     * @var DomainConfigurationInterface
     */
    private $domainConfiguration;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var string
     */
    private $menuEntityClass;

    public function __construct(array $menuNames, DomainConfigurationInterface $domainConfiguration, EntityManager $em, $menuEntityClass)
    {
        $this->menuNames = $menuNames;
        $this->domainConfiguration = $domainConfiguration;
        $this->em = $em;
        $this->menuEntityClass = $menuEntityClass;
    }

    /**
     * Make sure the menu objects exist in the database for each locale.
     */
    public function makeSureMenusExist()
    {
        $locales = array_unique($this->getLocales());
        $required = [];

        foreach ($this->menuNames as $name) {
            $required[$name] = $locales;
        }

        $menuObjects = $this->em->getRepository($this->menuEntityClass)->findAll();

        foreach ($menuObjects as $menu) {
            if (\array_key_exists($menu->getName(), $required)) {
                $index = array_search($menu->getLocale(), $required[$menu->getName()]);
                if ($index !== false) {
                    unset($required[$menu->getName()][$index]);
                }
            }
        }

        foreach ($required as $name => $locales) {
            foreach ($locales as $locale) {
                $className = $this->menuEntityClass;
                $menu = new $className();
                $menu->setName($name);
                $menu->setLocale($locale);
                $this->em->persist($menu);
            }
        }

        $this->em->flush();
    }

    private function getLocales(): array
    {
        return $this->domainConfiguration->getBackendLocales();
    }
}
