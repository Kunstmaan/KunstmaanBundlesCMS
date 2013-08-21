<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\Tools\EntityGenerator;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Generates all classes/files for a new pagepart
 */
class PagePartGenerator extends KunstmaanGenerator
{
    /**
     * @var BundleInterface
     */
    private $bundle;

    /**
     * @var string
     */
    private $entity;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var array
     */
    private $sections;

    /**
     * Generate the pagepart.
     *
     * @param BundleInterface $bundle         The bundle
     * @param string          $entity         The entity name
     * @param string          $prefix         The database prefix
     * @param array           $fields         The fields
     * @param array           $sections       The page sections
     *
     * @throws \RuntimeException
     */
    public function generate(BundleInterface $bundle, $entity, $prefix, array $fields, array $sections)
    {
        $this->bundle = $bundle;
        $this->entity = $entity;
        $this->prefix = $prefix;
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
            array($this->bundle->getName() => $this->bundle->getNamespace().'\\Entity\\PageParts'),
            $config->getEntityNamespaces()
        ));

        $entityClass = $this->registry->getEntityNamespace($this->bundle->getName()).'\\PageParts\\'.$this->entity;
        $entityPath = $this->bundle->getPath().'/Entity/PageParts/'.str_replace('\\', '/', $this->entity).'.php';
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
        $class->setPrimaryTable(array('name' => strtolower($this->prefix.strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $this->entity)))));
        $entityCode = $this->getEntityGenerator()->generateEntityClass($class);

        // Add some extra functions in the generated entity :s
        $params = array(
            'bundle' => $this->bundle->getName(),
            'pagepart' => $this->entity,
            'adminType' => '\\'.$this->bundle->getNamespace().'\\Form\\PageParts\\'.$this->entity.'AdminType'
        );
        $extraCode = $this->render('/Entity/PageParts/ExtraFunctions.php', $params);

        $pos = strrpos($entityCode, "}");
        $trimmed = substr($entityCode, 0, $pos);
        $entityCode = $trimmed."\n".$extraCode."\n}";

        $this->filesystem->mkdir(dirname($entityPath));
        file_put_contents($entityPath, $entityCode);

        $this->assistant->writeLine('Generating entity : <info>OK</info>');
    }

    /**
     * Generate the admin form type entity.
     */
    private function generateFormType()
    {
        $className = $this->entity.'AdminType';
        $savePath = $this->bundle->getPath().'/Form/PageParts/'.$className.'.php';
        $name = str_replace("\\", '_', strtolower($this->bundle->getNamespace())).'_'.strtolower($this->entity).'type';

        $params = array(
            'className' => $className,
            'name' => $name,
            'namespace' => $this->bundle->getNamespace(),
            'entity' => '\\'.$this->bundle->getNamespace().'\Entity\\PageParts\\'.$this->entity,
            'fields' => $this->fields
        );
        $this->renderFile('/Form/PageParts/AdminType.php', $savePath, $params);

        $this->assistant->writeLine('Generating form type : <info>OK</info>');
    }

    /**
     * Generate the twig template.
     */
    private function generateResourceTemplate()
    {
        $savePath = $this->bundle->getPath().'/Resources/views/PageParts/'.$this->entity.'/view.html.twig';

        $params = array(
            'pagepart' => strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $this->entity)),
            'fields' => $this->fields
        );
        $this->renderFile('/Resources/views/PageParts/view.html.twig', $savePath, $params);

        $this->assistant->writeLine('Generating template : <info>OK</info>');
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
                    'class' => $this->bundle->getNamespace().'\\Entity\\PageParts\\'.$this->entity
                );

                $ymlData = Yaml::dump($data, $inline = 2, $indent = 4, $exceptionOnInvalidType = false, $objectSupport = false);
                file_put_contents($dir.$section, $ymlData);
            }

            $this->assistant->writeLine('Updating section config : <info>OK</info>');
        }
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
