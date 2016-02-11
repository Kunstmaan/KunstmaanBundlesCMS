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
     * @var array
     */
    private $parentPages;

    /**
     * Generate the page.
     *
     * @param BundleInterface $bundle      The bundle
     * @param string          $entity      The entity name
     * @param string          $prefix      The database prefix
     * @param array           $fields      The fields
     * @param string          $template    The page template
     * @param array           $sections    The page sections
     * @param array           $parentPages The parent pages
     *
     * @throws \RuntimeException
     */
    public function generate(
        BundleInterface $bundle,
        $entity,
        $prefix,
        array $fields,
        $template,
        array $sections,
        array $parentPages
    ) {
        $this->bundle      = $bundle;
        $this->entity      = $entity;
        $this->prefix      = $prefix;
        $this->fields      = $fields;
        $this->template    = $template;
        $this->sections    = $sections;
        $this->parentPages = $parentPages;

        $this->generatePageEntity();
        $this->generatePageFormType();
        $this->generatePageTemplateConfiguration();
        $this->updateParentPages();
    }

    /**
     * Generate the page entity.
     *
     * @throws \RuntimeException
     */
    private function generatePageEntity()
    {
        list($entityCode, $entityPath) = $this->generateEntity(
            $this->bundle,
            $this->entity,
            $this->fields,
            'Pages',
            $this->prefix,
            'Kunstmaan\NodeBundle\Entity\AbstractPage'
        );

        // Add implements HasPageTemplateInterface
        $search     = 'extends \Kunstmaan\NodeBundle\Entity\AbstractPage';
        $entityCode = str_replace(
            $search,
            $search . ' implements \Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface',
            $entityCode
        );

        // Add some extra functions in the generated entity :s
        $params    = array(
            'bundle'    => $this->bundle->getName(),
            'page'      => $this->entity,
            'template'  => substr($this->template, 0, strlen($this->template) - 4),
            'sections'  => array_map(
                function ($val) {
                    return substr($val, 0, strlen($val) - 4);
                },
                $this->sections
            ),
            'adminType' => '\\' . $this->bundle->getNamespace() . '\\Form\\Pages\\' . $this->entity . 'AdminType',
            'namespace' => $this->registry->getAliasNamespace($this->bundle->getName()) . '\\Pages\\' . $this->entity
        );
        $extraCode = $this->render('/Entity/Pages/ExtraFunctions.php', $params);

        $pos        = strrpos($entityCode, '}');
        $trimmed    = substr($entityCode, 0, $pos);
        $entityCode = $trimmed . $extraCode . "\n}";

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
        $this->generateEntityAdminType(
            $this->bundle,
            $this->entity,
            'Pages',
            $this->fields,
            '\Kunstmaan\NodeBundle\Form\PageAdminType'
        );

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

    /**
     * Update the getPossibleChildTypes function of the parent Page classes
     */
    private function updateParentPages()
    {
        $phpCode = "            array(\n";
        $phpCode .= "                'name' => '" . $this->entity . "',\n";
        $phpCode .= "                'class'=> '" .
            $this->bundle->getNamespace() .
            "\\Entity\\Pages\\" . $this->entity . "'\n";
        $phpCode .= "            ),";

        // When there is a BehatTestPage, we should also allow the new page as sub page
        $behatTestPage = $this->bundle->getPath() . '/Entity/Pages/BehatTestPage.php';
        if (file_exists($behatTestPage)) {
            $this->parentPages[] = $behatTestPage;
        }

        foreach ($this->parentPages as $file) {
            $data = file_get_contents($file);
            $data = preg_replace(
                '/(function\s*getPossibleChildTypes\s*\(\)\s*\{\s*return\s*array\s*\()/',
                "$1\n$phpCode",
                $data
            );
            file_put_contents($file, $data);
        }
    }
}
