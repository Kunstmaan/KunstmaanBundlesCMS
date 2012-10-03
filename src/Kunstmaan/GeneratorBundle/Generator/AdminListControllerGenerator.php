<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\Mapping\ClassMetadata;


/**
 * Generates the controller for an AdminList
 */
class AdminListControllerGenerator extends \Sensio\Bundle\GeneratorBundle\Generator\Generator
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

        $this->className = $entityClass . 'AdminListController';
        $dirPath = $bundle->getPath() . '/Controller';
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
        );

        $this->renderFile($this->skeletonDir, 'EntityAdminListController.php', $dirPath . '/' . $entity . 'AdminListController.php', $parameters);

    }
}