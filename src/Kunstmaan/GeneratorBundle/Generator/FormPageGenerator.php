<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Generates all classes/files for a new formpage
 */
class FormPageGenerator extends KunstmaanGenerator
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
     * @var string
     */
    protected $skeletonDir;


    /**
     * Generate the formpage.
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
        $this->skeletonDir = __DIR__.'/../Resources/SensioGeneratorBundle/skeleton/formpage';

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
            'Kunstmaan\FormBundle\Entity\AbstractFormPage'
        );

        // Add implements HasPageTemplateInterface
        $search     = 'extends \Kunstmaan\FormBundle\Entity\AbstractFormPage';
        $entityCode = str_replace(
            $search,
            $search . ' implements \Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface',
            $entityCode
        );

        // Add extra configuration to the generated entity (for templates, etc)
        $params    = array(
            'bundle'    => $this->bundle->getName(),
            'page'      => $this->entity,
            'template'  => $this->template,
            'sections'  => $this->sections,
            'adminType' => '\\' . $this->bundle->getNamespace() . '\\Form\\Pages\\' . $this->entity . 'AdminType',
            'namespace' => $this->registry->getAliasNamespace($this->bundle->getName()) . '\\Pages\\' . $this->entity
        );

        $extraCode = $this->render('/Entity/Pages/ExtraFunctions.php', $params);
        $defaultTemplate = 'Pages:Common/view.html.twig';
        $formPageTemplate = 'Pages\\'.$this->entity.':view.html.twig';
        $extraCode = str_replace(
            $defaultTemplate,
            $formPageTemplate,
            $extraCode
        );

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
            '\Kunstmaan\FormBundle\Form\AbstractFormPageAdminType'
        );

        $this->assistant->writeLine('Generating form type : <info>OK</info>');
    }

    /**
     * Generate the page template and pagepart configuration.
     */
    private function generatePageTemplateConfiguration()
    {
        $this->copyTemplates();
        $this->installDefaultPagePartConfiguration($this->bundle);
        $this->copyTemplateConfig();
        $this->assistant->writeLine('Generating template configuration : <info>OK</info>');
    }

    /**
     * Copy and modify default formPage templates
     */
    private function copyTemplates()
    {
        $dirPath = $this->bundle->getPath();
        $this->filesystem->copy($this->skeletonDir . '/Resources/views/Pages/FormPage/view.html.twig', $dirPath . '/Resources/views/Pages/'.$this->entity.'/view.html.twig', true);
        $this->filesystem->copy($this->skeletonDir . '/Resources/views/Pages/FormPage/pagetemplate.html.twig', $dirPath . '/Resources/views/Pages/'.$this->entity.'/pagetemplate.html.twig', true);
        GeneratorUtils::replace("~~~BUNDLE~~~", $this->bundle->getName(), $dirPath . '/Resources/views/Pages/'.$this->entity.'/pagetemplate.html.twig');

        GeneratorUtils::prepend("{% extends '" . $this->bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Resources/views/Pages/'.$this->entity.'/view.html.twig');
    }

    /**
     * Copy and modify default config files for the pagetemplate and pageparts.
     */
    private function copyTemplateConfig()
    {
        $dirPath = $this->bundle->getPath();
        $pagepartFile = $dirPath . '/Resources/config/pageparts/'.$this->template.'.yml';
        $this->filesystem->copy($this->skeletonDir .'/Resources/config/pageparts/formpage.yml', $pagepartFile, false);
        GeneratorUtils::replace("~~~ENTITY~~~", $this->entity, $pagepartFile);

        $pagetemplateFile = $dirPath . '/Resources/config/pagetemplates/'.$this->template.'.yml';
        $this->filesystem->copy($this->skeletonDir .'/Resources/config/pagetemplates/formpage.yml', $pagetemplateFile, false);
        GeneratorUtils::replace("~~~BUNDLE~~~", $this->bundle->getName(), $pagetemplateFile);
        GeneratorUtils::replace("~~~ENTITY~~~", $this->entity, $pagetemplateFile);
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
