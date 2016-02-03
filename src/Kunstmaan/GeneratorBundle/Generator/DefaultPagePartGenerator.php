<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Generates all classes/files for a new pagepart
 */
class DefaultPagePartGenerator extends KunstmaanGenerator
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
    private $sections;

    /**
     * Generate the pagepart.
     *
     * @param BundleInterface $bundle    The bundle
     * @param string          $entity    The entity name
     * @param string          $prefix    The database prefix
     * @param array           $sections  The page sections
     * @param bool            $behatTest If we need to generate a behat test for this pagepart
     *
     * @throws \RuntimeException
     */
    public function generate(BundleInterface $bundle, $entity, $prefix, array $sections, $behatTest)
    {
        $this->bundle   = $bundle;
        $this->entity   = $entity;
        $this->prefix   = $prefix;
        $this->sections = $sections;

        $this->generatePagePartEntity();
        if ($entity != 'AbstractPagePart') {
            $this->generateFormType();
            $this->generateResourceTemplate();
            $this->generateSectionConfig();
            if ($behatTest) {
                $this->generateBehatTest();
            }
        }
    }

    /**
     * Generate the pagepart entity.
     */
    private function generatePagePartEntity()
    {
        $params = array(
            'bundle'         => $this->bundle->getName(),
            'namespace'      => $this->bundle->getNamespace(),
            'pagepart'       => $this->entity,
            'pagepartname'   => str_replace('PagePart', '', $this->entity),
            'adminType'      => '\\' . $this->bundle->getNamespace(
                ) . '\\Form\\PageParts\\' . $this->entity . 'AdminType',
            'underscoreName' => strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $this->entity)),
            'prefix'         => $this->prefix
        );

        $this->renderSingleFile(
            $this->skeletonDir . '/Entity/PageParts/' . $this->entity . '/',
            $this->bundle->getPath() . '/Entity/PageParts/',
            $this->entity . '.php',
            $params,
            true
        );

        $this->assistant->writeLine('Generating ' . $this->entity . ' Entity:       <info>OK</info>');
    }

    /**
     * Generate the admin form type entity.
     */
    private function generateFormType()
    {
        $params = array(
            'bundle'       => $this->bundle->getName(),
            'namespace'    => $this->bundle->getNamespace(),
            'pagepart'     => $this->entity,
            'pagepartname' => str_replace('PagePart', '', $this->entity),
            'adminType'    => '\\' . $this->bundle->getNamespace() . '\\Form\\PageParts\\' . $this->entity . 'AdminType'
        );

        $this->renderSingleFile(
            $this->skeletonDir . '/Form/PageParts/' . $this->entity . '/',
            $this->bundle->getPath() . '/Form/PageParts/',
            $this->entity . 'AdminType.php',
            $params,
            true
        );

        $this->assistant->writeLine('Generating ' . $this->entity . ' FormType:     <info>OK</info>');
    }

    /**
     * Generate the twig template.
     */
    private function generateResourceTemplate()
    {
        $params = array(
            'pagepart' => strtolower(
                    preg_replace('/([a-z])([A-Z])/', '$1-$2', str_ireplace('PagePart', '', $this->entity))
                ) . '-pp',
        );

        $this->renderSingleFile(
            $this->skeletonDir . '/Resources/views/PageParts/' . $this->entity . '/',
            $this->bundle->getPath() . '/Resources/views/PageParts/' . $this->entity . '/',
            'view.html.twig',
            $params,
            true
        );

        $this->assistant->writeLine('Generating ' . $this->entity . ' template:     <info>OK</info>');
    }

    /**
     * Update the page section config files
     */
    private function generateSectionConfig()
    {
        if (count($this->sections) > 0) {
            $dir = $this->bundle->getPath() . '/Resources/config/pageparts/';
            foreach ($this->sections as $section) {
                $data = Yaml::parse($dir . $section);
                if (!array_key_exists('types', $data)) {
                    $data['types'] = array();
                }
                $class = $this->bundle->getNamespace() . '\\Entity\\PageParts\\' . $this->entity;
                $found = false;
                foreach ($data['types'] as $type) {
                    if ($type['class'] == $class) {
                        $found = true;
                    }
                }

                if (!$found) {
                    $data['types'][] = array(
                        'name'  => str_replace('PagePart', '', $this->entity),
                        'class' => $class
                    );
                }

                $ymlData = Yaml::dump($data);
                file_put_contents($dir . $section, $ymlData);
            }

            $this->assistant->writeLine('Updating ' . $this->entity . ' section config: <info>OK</info>');
        }
    }

    /**
     * Generate the admin form type entity.
     */
    private function generateBehatTest()
    {
        // TODO

        $this->assistant->writeLine('Generating behat test : <info>OK</info>');
    }
}
