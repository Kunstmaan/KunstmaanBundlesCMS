<?php

namespace Kunstmaan\MenuBundle\Service;

use Kunstmaan\MenuBundle\Entity\Menu;

class MenuService
{
    /**
     * @var array
     */
    private $menuNames;

    /**
     * @var \Kunstmaan\NodeBundle\Helper\DomainConfiguration
     */
    private $domainConfiguration;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @param array  $menuNames
     * @param \Kunstmaan\NodeBundle\Helper\DomainConfiguration $domainConfiguration
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(
        array $menuNames,
        $domainConfiguration,
        $em
    ) {
        $this->menuNames = $menuNames;
        $this->domainConfiguration = $domainConfiguration;
        $this->em = $em;
    }

    /**
     * Make sure the menu objects exist in the database for each locale.
     */
    public function makeSureMenusExist()
    {
        $locales = $this->getLocales();
        $required = array();

        foreach ($this->menuNames as $name) {
            $required[$name] = $locales;
        }

        $menuObjects = $this->em->getRepository('KunstmaanMenuBundle:Menu')->findAll();

        foreach ($menuObjects as $menu) {
            if (array_key_exists($menu->getName(), $required)) {
                $index = array_search(
                    $menu->getLocale(),
                    $required[$menu->getName()]
                );
                if ($index !== false) {
                    unset($required[$menu->getName()][$index]);
                }
            }
        }

        foreach ($required as $name => $locales) {
            foreach ($locales as $locale) {
                $menu = new Menu();
                $menu->setName($name);
                $menu->setLocale($locale);
                $this->em->persist($menu);
            }
        }

        $this->em->flush();
    }

    private function getLocales()
    {
        return $this->domainConfiguration
            ->getBackendLocales();
    }
}
