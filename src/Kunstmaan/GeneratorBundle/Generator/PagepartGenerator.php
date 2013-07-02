<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\Tools\EntityGenerator;
use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Sensio\Bundle\GeneratorBundle\Generator\Generator;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Generates all classes/files for a new pagepart
 */
class PagepartGenerator extends Generator
{

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var RegistryInterface
     */
    private $registry;

    /**
     * @var string
     */
    private $skeletonDir;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var BundleInterface
     */
    private $bundle;

    /**
     * @var string
     */
    private $entity;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var array
     */
    private $sections;

    /**
     * @param Filesystem        $filesystem  The filesystem
     * @param RegistryInterface $registry    The registry
     * @param string            $skeletonDir The directory of the skeleton
     * @param OutputInterface   $output      The output
     */
    public function __construct(Filesystem $filesystem, RegistryInterface $registry, $skeletonDir, OutputInterface $output)
    {
        $this->filesystem = $filesystem;
        $this->registry = $registry;
        $this->skeletonDir = GeneratorUtils::getFullSkeletonPath($skeletonDir);
        $this->output = $output;

        $this->setSkeletonDirs(array($this->skeletonDir));
    }

    /**
     * Generate the pagepart.
     *
     * @param BundleInterface $bundle         The bundle
     * @param string          $entity         The entity name
     * @param array           $fields         The fields
     * @param array           $sections       The page sections
     *
     * @throws \RuntimeException
     */
    public function generate(BundleInterface $bundle, $entity, array $fields, array $sections)
    {
        $this->bundle = $bundle;
        $this->entity = $entity;
        $this->fields = $fields;
        $this->sections = $sections;

        $this->generateEntity();
        $this->generateFormType();
        $this->generateResourceTemplate();
        $this->generateSectionConfig();
    }

    /**
     * Generate the pagepart entity.
     *
     * @throws \RuntimeException
     */
    private function generateEntity()
    {
        // configure the bundle (needed if the bundle does not contain any Entities yet)
        $config = $this->registry->getEntityManager(null)->getConfiguration();
        $config->setEntityNamespaces(array_merge(
            array($this->bundle->getName() => $this->bundle->getNamespace().'\\Entity\\Pageparts'),
            $config->getEntityNamespaces()
        ));

        $entityClass = $this->registry->getEntityNamespace($this->bundle->getName()).'\\Pageparts\\'.$this->entity;
        $entityPath = $this->bundle->getPath().'/Entity/Pageparts/'.str_replace('\\', '/', $this->entity).'.php';
        if (file_exists($entityPath)) {
            throw new \RuntimeException(sprintf('Entity "%s" already exists.', $entityClass));
        }

        $class = new ClassMetadataInfo($entityClass, new UnderscoreNamingStrategy());
        foreach ($this->fields as $fieldArray) {
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
        list($project, $bundle) = explode("\\", $this->bundle->getNameSpace());
        $class->setPrimaryTable(array('name' => strtolower($project.'__'.$this->entity)));
        $entityCode = $this->getEntityGenerator()->generateEntityClass($class);

        // Add some extra functions in the generated entity :s
        $params = array(
            'bundle' => $this->bundle->getName(),
            'pagepart' => $this->entity,
            'adminType' => '\\'.$this->bundle->getNamespace().'\\Form\\Pageparts\\'.$this->entity.'AdminType'
        );
        $extraCode = $this->render('/Entity/Pageparts/ExtraFunctions.php', $params);

        $pos = strrpos($entityCode, "}");
        $trimmed = substr($entityCode, 0, $pos);
        $entityCode = $trimmed."\n".$extraCode."\n}";

        $this->filesystem->mkdir(dirname($entityPath));
        file_put_contents($entityPath, $entityCode);

        $this->output->writeln('Generating entity : <info>OK</info>');
    }

    /**
     * Generate the admin form type entity.
     */
    private function generateFormType()
    {
        $className = $this->entity.'AdminType';
        $savePath = $this->bundle->getPath().'/Form/Pageparts/'.$className.'.php';
        $name = str_replace("\\", '_', strtolower($this->bundle->getNamespace())).'_'.strtolower($this->entity).'type';

        $params = array(
            'className' => $className,
            'name' => $name,
            'namespace' => $this->bundle->getNamespace(),
            'entity' => '\\'.$this->bundle->getNamespace().'\Entity\\Pageparts\\'.$this->entity,
            'fields' => $this->fields
        );
        $this->renderFile('/Form/Pageparts/AdminType.php', $savePath, $params);

        $this->output->writeln('Generating form type : <info>OK</info>');
    }

    /**
     * Generate the twig template.
     */
    private function generateResourceTemplate()
    {
        $savePath = $this->bundle->getPath().'/Resources/views/Pageparts/'.$this->entity.'/view.html.twig';

        $params = array(
            'pagepart' => $this->entity,
            'fields' => $this->fields
        );
        $this->renderFile('/Resources/views/Pageparts/view.html.twig', $savePath, $params);

        $this->output->writeln('Generating template : <info>OK</info>');
    }

    /**
     * Update the page section config files
     */
    private function generateSectionConfig()
    {
        if (count($this->sections) > 0) {
            $dir = $this->bundle->getPath().'/Resources/config/pageparts/';
            foreach ($this->sections as $section) {
                $data = Yaml::parse($dir.$section);
                if (!array_key_exists('types', $data)) {
                    $data['types'] = array();
                }
                $data['types'][] = array(
                    'name' => str_replace('PagePart', '', $this->entity),
                    'class' => $this->bundle->getNamespace().'\\Entity\\Pageparts\\'.$this->entity
                );

                $ymlData = Yaml::dump($data, $inline = 2, $indent = 4, $exceptionOnInvalidType = false, $objectSupport = false);
                file_put_contents($dir.$section, $ymlData);
            }

            $this->output->writeln('Updating section config : <info>OK</info>');
        }
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
     * Get a Doctrine EntityGenerator instance.
     *
     * @return \Doctrine\ORM\Tools\EntityGenerator
     */
    private function getEntityGenerator()
    {
        $entityGenerator = new EntityGenerator();
        $entityGenerator->setClassToExtend('Kunstmaan\PagePartBundle\Entity\AbstractPagePart');
        $entityGenerator->setGenerateAnnotations(true);
        $entityGenerator->setGenerateStubMethods(true);
        $entityGenerator->setRegenerateEntityIfExists(false);
        $entityGenerator->setUpdateEntityIfExists(true);
        $entityGenerator->setNumSpaces(4);
        $entityGenerator->setAnnotationPrefix('ORM\\');

        return $entityGenerator;
    }

}
