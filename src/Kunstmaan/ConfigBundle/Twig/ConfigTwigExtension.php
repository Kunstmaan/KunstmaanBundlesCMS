<?php

namespace Kunstmaan\ConfigBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\ConfigBundle\Entity\AbstractConfig;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Extension to fetch config
 *
 * @final since 5.4
 */
class ConfigTwigExtension extends AbstractExtension
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var array
     */
    private $configuration;

    /**
     * @var array
     */
    private $configs = [];

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
        return [
            new TwigFunction(
                'get_config_by_internal_name', [$this, 'getConfigByInternalName']
            ),
        ];
    }

    /**
     * @param string $internalName Internal name of the site config entity
     *
     * @return AbstractConfig
     */
    public function getConfigByInternalName($internalName)
    {
        if (\in_array($internalName, $this->configs)) {
            return $this->configs[$internalName];
        }

        foreach ($this->configuration['entities'] as $class) {
            $entity = new $class();

            if ($entity->getInternalName() == $internalName) {
                $repo = $this->em->getRepository($class);
                $config = $repo->findOneBy([]);

                $this->configs[$internalName] = $config;

                return $config;
            }
        }

        return null;
    }
}
