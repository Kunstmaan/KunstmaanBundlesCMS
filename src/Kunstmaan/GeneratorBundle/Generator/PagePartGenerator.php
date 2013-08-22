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

        $this->generatePagePartEntity();
        $this->generateFormType();
        $this->generateResourceTemplate();
        $this->generateSectionConfig();
    }

    /**
     * Generate the pagepart entity.
     *
     * @throws \RuntimeException
     */
    private function generatePagePartEntity()
    {
        list($entityCode, $entityPath) = $this->generateEntity($this->bundle, $this->entity, $this->fields, 'PageParts', $this->prefix, 'Kunstmaan\PagePartBundle\Entity\AbstractPagePart');

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

        // Write class to filesystem
        $this->filesystem->mkdir(dirname($entityPath));
        file_put_contents($entityPath, $entityCode);

        $this->assistant->writeLine('Generating entity : <info>OK</info>');
    }

    /**
     * Generate the admin form type entity.
     */
    private function generateFormType()
    {
        $this->generateEntityAdminType($this->bundle, $this->entity, 'PageParts', $this->fields);

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
}
