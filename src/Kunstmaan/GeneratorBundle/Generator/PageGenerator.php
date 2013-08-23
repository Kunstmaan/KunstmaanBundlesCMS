<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Generates all classes/files for a new page
 */
class PageGenerator extends KunstmaanGenerator
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
     * @var string
     */
    private $template;

    /**
     * @var array
     */
    private $sections;

    /**
     * Generate the page.
     *
     * @param BundleInterface $bundle         The bundle
     * @param string          $entity         The entity name
     * @param string          $prefix         The database prefix
     * @param array           $fields         The fields
     * @param string          $template       The page template
     * @param array           $sections       The page sections
     *
     * @throws \RuntimeException
     */
    public function generate(BundleInterface $bundle, $entity, $prefix, array $fields, $template, array $sections)
    {
        $this->bundle = $bundle;
        $this->entity = $entity;
        $this->prefix = $prefix;
        $this->fields = $fields;
        $this->template = $template;
        $this->sections = $sections;

        $this->generatePageEntity();
        $this->generatePageFormType();
        $this->generatePageTemplateConfiguration();
    }

    /**
     * Generate the page entity.
     *
     * @throws \RuntimeException
     */
    private function generatePageEntity()
    {
        list($entityCode, $entityPath) = $this->generateEntity($this->bundle, $this->entity, $this->fields, 'Pages', $this->prefix, 'Kunstmaan\NodeBundle\Entity\AbstractPage');

        // Add implements HasPageTemplateInterface
        $search = 'extends \Kunstmaan\NodeBundle\Entity\AbstractPage';
        $entityCode = str_replace($search, $search.' implements \Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface', $entityCode);

        // Add some extra functions in the generated entity :s
        $params = array(
            'bundle' => $this->bundle->getName(),
            'page' => $this->entity,
            'template' => substr($this->template, 0, strlen($this->template)-4),
            'sections' => array_map(function($val) { return substr($val, 0, strlen($val)-4); }, $this->sections),
            'adminType' => '\\'.$this->bundle->getNamespace().'\\Form\\Pages\\'.$this->entity.'AdminType',
            'namespace' => $this->registry->getEntityNamespace($this->bundle->getName()).'\\Pages\\'.$this->entity
        );
        $extraCode = $this->render('/Entity/Pages/ExtraFunctions.php', $params);

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
    private function generatePageFormType()
    {
        $this->generateEntityAdminType($this->bundle, $this->entity, 'Pages', $this->fields, '\Kunstmaan\NodeBundle\Form\PageAdminType');

        $this->assistant->writeLine('Generating form type : <info>OK</info>');
    }

    /**
     * Generate the page template -and pagepart configuration.
     */
    private function generatePageTemplateConfiguration()
    {
        $this->installDefaultPageTemplates($this->bundle);
        $this->installDefaultPagePartConfiguration($this->bundle);

        $this->assistant->writeLine('Generating template configuration : <info>OK</info>');
    }
}
