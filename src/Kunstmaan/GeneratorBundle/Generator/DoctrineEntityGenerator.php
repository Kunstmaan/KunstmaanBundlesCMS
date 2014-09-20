<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\EntityGenerator;
use Doctrine\ORM\Tools\EntityRepositoryGenerator;
use Sensio\Bundle\GeneratorBundle\Generator\Generator;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * DoctrineEntityGenerator
 */
class DoctrineEntityGenerator extends Generator
{
    private $filesystem;
    private $registry;

    /**
     * @param Filesystem        $filesystem The filesystem
     * @param RegistryInterface $registry   The registry
     */
    public function __construct(Filesystem $filesystem, RegistryInterface $registry)
    {
        $this->filesystem = $filesystem;
        $this->registry = $registry;
    }

    /**
     * @param BundleInterface $bundle         The bundle
     * @param string          $entity         The entity name
     * @param string          $format         The format
     * @param array           $fields         The fields
     * @param boolean         $withRepository With repository
     * @param string          $prefix         A prefix
     *
     * @throws \RuntimeException
     */
    public function generate(BundleInterface $bundle, $entity, $format, array $fields, $withRepository, $prefix)
    {
        // configure the bundle (needed if the bundle does not contain any Entities yet)
        $config = $this->registry->getEntityManager(null)->getConfiguration();
        $config->setEntityNamespaces(array_merge(
            array($bundle->getName() => $bundle->getNamespace().'\\Entity'),
            $config->getEntityNamespaces()
        ));

        $entityClass = $this->registry->getEntityNamespace($bundle->getName()).'\\'.$entity;
        $entityPath = $bundle->getPath().'/Entity/'.str_replace('\\', '/', $entity).'.php';
        if (file_exists($entityPath)) {
            throw new \RuntimeException(sprintf('Entity "%s" already exists.', $entityClass));
        }

        $class = new ClassMetadataInfo($entityClass);
        if ($withRepository) {
            $entityClass = preg_replace('/\\\\Entity\\\\/', '\\Repository\\', $entityClass, 1);
            $class->customRepositoryClassName = $entityClass.'Repository';
        }

        foreach ($fields as $field) {
            $class->mapField($field);
        }

        $class->setPrimaryTable(array('name' => $prefix . $this->getTableNameFromEntityName($entity)));

        $entityGenerator = $this->getEntityGenerator();

        $entityCode = $entityGenerator->generateEntityClass($class);
        $mappingPath = $mappingCode = false;

        $this->filesystem->mkdir(dirname($entityPath));
        file_put_contents($entityPath, $entityCode);

        if ($mappingPath) {
            $this->filesystem->mkdir(dirname($mappingPath));
            file_put_contents($mappingPath, $mappingCode);
        }

        if ($withRepository) {
            $path = $bundle->getPath().str_repeat('/..', substr_count(get_class($bundle), '\\'));
            $this->getRepositoryGenerator()->writeEntityRepositoryClass($class->customRepositoryClassName, $path);
        }
    }

    private function getTableNameFromEntityName($entityName)
    {
        // Only look at the last part. We split on '\'
        $entityName = str_replace('\\', '', $entityName);
        return $this->convertCamelCaseToSnakeCase($entityName);
    }

    public static function convertCamelCaseToSnakeCase($text)
    {
        $text = preg_replace_callback('/[A-Z]/', create_function('$match', 'return "_" . strtolower($match[0]);'), $text);
        // remove first underscore.
        $text = preg_replace('/^_/', '', $text);
        return strtolower($text);
    }

    /**
     * @param string $keyword
     *
     * @return boolean
     */
    public function isReservedKeyword($keyword)
    {
        return $this->registry->getConnection()->getDatabasePlatform()->getReservedKeywordsList()->isKeyword($keyword);
    }

    /**
     * @return \Doctrine\ORM\Tools\EntityGenerator
     */
    protected function getEntityGenerator()
    {
        $entityGenerator = new EntityGenerator();
        $entityGenerator->setClassToExtend('Kunstmaan\AdminBundle\Entity\AbstractEntity');
        $entityGenerator->setGenerateAnnotations(true);
        $entityGenerator->setGenerateStubMethods(true);
        $entityGenerator->setRegenerateEntityIfExists(false);
        $entityGenerator->setUpdateEntityIfExists(true);
        $entityGenerator->setNumSpaces(4);
        $entityGenerator->setAnnotationPrefix('ORM\\');

        return $entityGenerator;
    }

    /**
     * @return \Doctrine\ORM\Tools\EntityRepositoryGenerator
     */
    protected function getRepositoryGenerator()
    {
        return new EntityRepositoryGenerator();
    }
}
