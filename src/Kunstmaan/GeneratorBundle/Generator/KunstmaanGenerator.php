<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\Tools\EntityGenerator;
use Kunstmaan\GeneratorBundle\Helper\CommandAssistant;
use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Sensio\Bundle\GeneratorBundle\Generator\Generator;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Class that contains all common generator logic.
 */
class KunstmaanGenerator extends Generator
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @var string
     */
    protected $skeletonDir;

    /**
     * @var CommandAssistant
     */
    protected $assistant;

    /**
     * @param Filesystem        $filesystem  The filesystem
     * @param RegistryInterface $registry    The registry
     * @param string            $skeletonDir The directory of the skeleton
     * @param CommandAssistant  $assistant  The command assistant
     */
    public function __construct(Filesystem $filesystem, RegistryInterface $registry, $skeletonDir, CommandAssistant $assistant)
    {
        $this->filesystem = $filesystem;
        $this->registry = $registry;
        $this->skeletonDir = GeneratorUtils::getFullSkeletonPath($skeletonDir);
        $this->assistant = $assistant;

        $this->setSkeletonDirs(array($this->skeletonDir));
    }

    /**
     * Check that the keyword is a reserved word for the database system.
     *
     * @param string $keyword
     * @return boolean
     */
    public function isReservedKeyword($keyword)
    {
        return $this->registry->getConnection()->getDatabasePlatform()->getReservedKeywordsList()->isKeyword($keyword);
    }

    /**
     * Generate the entity PHP code.
     *
     * @param BundleInterface $bundle
     * @param string $name
     * @param string $namePrefix
     * @param string $dbPrefix
     * @param string|null $extendClass
     * @return array
     * @throws \RuntimeException
     */
    protected function generateEntity(BundleInterface $bundle, $name, $namePrefix, $dbPrefix, $extendClass = null)
    {
        // configure the bundle (needed if the bundle does not contain any Entities yet)
        $config = $this->registry->getEntityManager(null)->getConfiguration();
        $config->setEntityNamespaces(array_merge(
            array($bundle->getName() => $bundle->getNamespace().'\\Entity\\'.$namePrefix),
            $config->getEntityNamespaces()
        ));

        $entityClass = $this->registry->getEntityNamespace($bundle->getName()).'\\'.$namePrefix.'\\'.$name;
        $entityPath = $bundle->getPath().'/Entity/'.$namePrefix.'/'.str_replace('\\', '/', $name).'.php';
        if (file_exists($entityPath)) {
            throw new \RuntimeException(sprintf('Entity "%s" already exists.', $entityClass));
        }

        $class = new ClassMetadataInfo($entityClass, new UnderscoreNamingStrategy());
        foreach ($this->fields as $fieldSet) {
            foreach ($fieldSet as $fieldArray) {
                foreach ($fieldArray as $field) {
                    if (array_key_exists('joinColumn', $field)) {
                        $class->mapManyToOne($field);
                    } elseif (array_key_exists('joinTable', $field)) {
                        $class->mapManyToMany($field);
                    } else {
                        $class->mapField($field);
                    }
                }
            }
        }
        $class->setPrimaryTable(array('name' => strtolower($dbPrefix.strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name)))));
        $entityCode = $this->getEntityGenerator($extendClass)->generateEntityClass($class);

        return array($entityCode, $entityPath);
    }

    /**
     * Get a Doctrine EntityGenerator instance.
     *
     * @param string|null $classToExtend
     * @return EntityGenerator
     */
    protected function getEntityGenerator($classToExtend = null)
    {
        $entityGenerator = new EntityGenerator();
        if (!is_null($classToExtend)) {
            $entityGenerator->setClassToExtend($classToExtend);
        }
        $entityGenerator->setGenerateAnnotations(true);
        $entityGenerator->setGenerateStubMethods(true);
        $entityGenerator->setRegenerateEntityIfExists(false);
        $entityGenerator->setUpdateEntityIfExists(true);
        $entityGenerator->setNumSpaces(4);
        $entityGenerator->setAnnotationPrefix('ORM\\');

        return $entityGenerator;
    }
}
