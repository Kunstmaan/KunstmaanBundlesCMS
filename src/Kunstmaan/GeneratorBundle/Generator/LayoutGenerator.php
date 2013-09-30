<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Generates all layout files
 */
class LayoutGenerator extends KunstmaanGenerator
{
    /**
     * @var BundleInterface
     */
    private $bundle;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * Generate the basic layout.
     *
     * @param BundleInterface $bundle         The bundle
     * @param string          $rootDir        The root directory of the application
     * @throws \RuntimeException
     */
    public function generate(BundleInterface $bundle, $rootDir)
    {
        $this->bundle = $bundle;
        $this->rootDir = $rootDir;

        $parameters = array(
            'namespace'         => $bundle->getNamespace(),
            'bundle'            => $bundle,
            'bundle_name'       => $bundle->getName(),
            'prefix'            => GeneratorUtils::cleanPrefix($prefix)
        );

        $this->generateGruntFiles($parameters);
        //$this->generatePagePartEntity();
        //$this->generateFormType();
        //$this->generateResourceTemplate();
        //$this->generateSectionConfig();
    }

    /**
     * Generate the grunt configuration files.
     *
     * @param array $parameters
     */
    public function generateGruntFiles(array $parameters)
    {
        $this->renderFiles($this->skeletonDir.'/grunt/', $parameters, true);

        $this->assistant->writeLine('Generating grunt configuration : <info>OK</info>');
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
            'pagepart' => strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', str_ireplace('PagePart', '', $this->entity))) . '-pp',
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
