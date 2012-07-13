<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\Mapping\ClassMetadata;

class AdminListTypeGenerator extends \Sensio\Bundle\GeneratorBundle\Generator\Generator {

 	private $filesystem;
    private $skeletonDir;

    public function __construct(Filesystem $filesystem, $skeletonDir) {
        $this->filesystem = $filesystem;
        $this->skeletonDir = $skeletonDir;
    }

    public function generate($bundle, $entity, $metadata) {
    	$parts       = explode('\\', $entity);
        $entityClass = array_pop($parts);

        $this->className = $entityClass.'AdminListType';
        $dirPath         = $bundle->getPath().'/Form';
        $this->classPath = $dirPath.'/'.str_replace('\\', '/', $entity).'.php';

        if (file_exists($this->classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $this->className, $this->classPath));
        }

    	$parts = explode('\\', $entity);
        array_pop($parts);

        $parameters = array(
            'namespace'        => $bundle->getNamespace(),
        	'bundle' 		   => $bundle,
            'entity_class'     => $entityClass,
        	'fields' => $this->getFieldsFromMetadata($metadata)
        );

        $this->renderFile($this->skeletonDir, 'EntityAdminListType.php', $dirPath.'/'.$entity.'AdminListType.php', $parameters);

    }

	private function getFieldsFromMetadata(ClassMetadata $metadata) {
        return GeneratorUtils::getFieldsFromMetadata($metadata);
    }

}