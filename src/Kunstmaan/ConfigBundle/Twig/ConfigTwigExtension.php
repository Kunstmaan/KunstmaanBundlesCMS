<?php

namespace Kunstmaan\ConfigBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\ConfigBundle\Entity\AbstractConfig;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig_Extension;

/**
 * Extension to fetch config
 */
class ConfigTwigExtension extends Twig_Extension
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @var array $configuration
     */
    private $configuration;

    /**
     * @var array
     */
    private $configs = array();

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, $configuration)
    {
        $this->em = $em;
        $this->configuration = $configuration;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction(
                'get_config_by_internal_name', array($this, 'getConfigByInternalName')
            ),
        );
    }

    /**
     * @param string $internalName Internal name of the site config entity
     *
     * @return AbstractConfig
     */
    public function getConfigByInternalName($internalName)
    {
        if (in_array($internalName, $this->configs)) {
            return $this->configs[$internalName];
        }

        foreach ($this->configuration['entities'] as $class) {
            $entity = new $class;

            if ($entity->getInternalName() == $internalName) {
                $repo = $this->em->getRepository($class);
                $config = $repo->findOneBy(array());

                $this->configs[$internalName] = $config;

                return $config;
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kunstmaan_config_twig_extension';
    }
}
