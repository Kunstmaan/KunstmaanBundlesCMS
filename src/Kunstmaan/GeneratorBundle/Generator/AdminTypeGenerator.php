<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * AdminTypeGenerator
 */
class AdminTypeGenerator extends \Sensio\Bundle\GeneratorBundle\Generator\Generator
{

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $skeletonDir;

    /**
     * @param Filesystem $filesystem  The filesystem
     * @param string     $skeletonDir The skeleton directory
     */
    public function __construct(Filesystem $filesystem, $skeletonDir)
    {
        $this->filesystem = $filesystem;
        $this->skeletonDir = $skeletonDir;
    }

    /**
     * @param Bundle        $bundle   The bundle
     * @param string        $entity   The entity name
     * @param ClassMetadata $metadata The meta data
     *
     * @throws \RuntimeException
     */
    public function generate(Bundle $bundle, $entity, ClassMetadata $metadata)
    {
        $parts = explode('\\', $entity);
        $entityClass = array_pop($parts);

        $className = $entityClass . 'AdminListType';
        $dirPath = $bundle->getPath() . '/Form';
        $classPath = $dirPath . '/' . str_replace('\\', '/', $entity) . '.php';

        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $className, $classPath));
        }

        $parts = explode('\\', $entity);
        array_pop($parts);

        $parameters = array(
            'namespace'         => $bundle->getNamespace(),
            'bundle'            => $bundle,
            'entity_class'      => $entityClass,
            'fields'            => $this->getFieldsFromMetadata($metadata)
        );

        $this->renderFile($this->skeletonDir, 'EntityAdminListType.php', $dirPath . '/' . $entity . 'AdminListType.php', $parameters);

    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return string[]
     */
    private function getFieldsFromMetadata(ClassMetadata $metadata)
    {
        return GeneratorUtils::getFieldsFromMetadata($metadata);
    }

}