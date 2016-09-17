<?php

namespace Kunstmaan\UtilitiesBundle\Helper;

use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FqcnLookup
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get fqcn name of object|string
     *
     * @param string|object $type
     *
     * @return string
     */
    public function getFqcn($type)
    {
        $fqcn = is_object($type) ? ClassUtils::getClass($type) : $type;
        if (!is_object($type) && is_string($type)) {
            if ($this->container->has($type)) {
                $fqcn = ClassUtils::getClass($this->container->get($type));
            }
        }
        return $fqcn;
    }
}
