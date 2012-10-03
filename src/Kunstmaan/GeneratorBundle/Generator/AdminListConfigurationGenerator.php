<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\Mapping\ClassMetadata;


/**
 * Generates the configuration for an AdminList
 */
class AdminListConfigurationGenerator extends \Sensio\Bundle\GeneratorBundle\Generator\Generator
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
     * @param string     $skeletonDir The directory of the skeleton
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

        $this->className = $entityClass . 'AdminListConfigurator';
        $dirPath = $bundle->getPath() . '/AdminList';
        $this->classPath = $dirPath . '/' . str_replace('\\', '/', $entity) . '.php';

        if (file_exists($this->classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $this->className, $this->classPath));
        }

        $parts = explode('\\', $entity);
        array_pop($parts);

        $parameters = array(
            'namespace'         => $bundle->getNamespace(),
            'bundle'            => $bundle,
            'entity_class'      => $entityClass,
            'fields'            => $this->getFieldsFromMetadata($metadata)
        );

        $this->renderFile($this->skeletonDir, 'AdminListConfigurator.php', $dirPath . '/' . $entity . 'AdminListConfigurator.php', $parameters);
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
