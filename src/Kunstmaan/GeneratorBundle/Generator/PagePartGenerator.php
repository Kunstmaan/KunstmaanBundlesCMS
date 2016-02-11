<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Faker\Provider\Lorem;
use Faker\Provider\DateTime;
use Faker\Provider\Base;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Finder\Finder;

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
     * @param BundleInterface $bundle    The bundle
     * @param string          $entity    The entity name
     * @param string          $prefix    The database prefix
     * @param array           $fields    The fields
     * @param array           $sections  The page sections
     * @param bool            $behatTest If we need to generate a behat test for this pagepart
     *
     * @throws \RuntimeException
     */
    public function generate(BundleInterface $bundle, $entity, $prefix, array $fields, array $sections, $behatTest)
    {
        $this->bundle   = $bundle;
        $this->entity   = $entity;
        $this->prefix   = $prefix;
        $this->fields   = $fields;
        $this->sections = $sections;

        $this->generatePagePartEntity();
        $this->generateFormType();
        $this->generateResourceTemplate();
        $this->generateSectionConfig();
        if ($behatTest) {
            $this->generateBehatTest();
        }
    }

    /**
     * Generate the pagepart entity.
     *
     * @throws \RuntimeException
     */
    private function generatePagePartEntity()
    {
        if (file_exists($this->bundle->getPath() . '/Entity/PageParts/AbstractPagePart.php')) {
            $abstractClass = $this->bundle->getNamespace() . '\Entity\PageParts\AbstractPagePart';
        } else {
            $abstractClass = 'Kunstmaan\PagePartBundle\Entity\AbstractPagePart';
        }

        list($entityCode, $entityPath) = $this->generateEntity(
            $this->bundle,
            $this->entity,
            $this->fields,
            'PageParts',
            $this->prefix,
            $abstractClass
        );

        // Add some extra functions in the generated entity :s
        $params    = array(
            'bundle'    => $this->bundle->getName(),
            'pagepart'  => $this->entity,
            'adminType' => '\\' . $this->bundle->getNamespace() . '\\Form\\PageParts\\' . $this->entity . 'AdminType'
        );
        $extraCode = $this->render('/Entity/PageParts/ExtraFunctions.php', $params);

        $pos        = strrpos($entityCode, "}");
        $trimmed    = substr($entityCode, 0, $pos);
        $entityCode = $trimmed . "\n" . $extraCode . "\n}";

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
        $savePath = $this->bundle->getPath() . '/Resources/views/PageParts/' . $this->entity . '/view.html.twig';

        $params = array(
            'pagepart' => strtolower(
                    preg_replace('/([a-z])([A-Z])/', '$1-$2', str_ireplace('PagePart', '', $this->entity))
                ) . '-pp',
            'fields'   => $this->fields
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
            $dir = $this->bundle->getPath() . '/Resources/config/pageparts/';
            foreach ($this->sections as $section) {
                $data = Yaml::parse($dir . $section);
                if (!array_key_exists('types', $data)) {
                    $data['types'] = array();
                }
                $data['types'][] = array(
                    'name'  => str_replace('PagePart', '', $this->entity),
                    'class' => $this->bundle->getNamespace() . '\\Entity\\PageParts\\' . $this->entity
                );

                $ymlData = Yaml::dump($data);
                file_put_contents($dir . $section, $ymlData);
            }

            $this->assistant->writeLine('Updating section config : <info>OK</info>');
        }
    }

    /**
     * Generate the admin form type entity.
     */
    private function generateBehatTest()
    {
        $configDir = $this->bundle->getPath() . '/Resources/config';

        // Get the context names for each section config file
        $sectionInfo = array();
        $dir         = $configDir . '/pageparts/';
        foreach ($this->sections as $section) {
            $data                                    = Yaml::parse($dir . $section);
            $sectionInfo[basename($section, '.yml')] = array('context' => $data['context'], 'pagetempates' => array());
        }

        /*
            Example $sectionInfo contents:
            Array
            (
                [main] => Array
                    (
                        [context] => main
                        [pagetempates] => Array
                            (
                            )
                    )
            )
        */

        // Get a list of page templates that use this context
        $templateFinder = new Finder();
        $templateFinder->files()->in($configDir . '/pagetemplates')->name('*.yml');

        $contextTemplates = array();
        foreach ($templateFinder as $templatePath) {
            $parts    = explode("/", $templatePath);
            $fileName = basename($parts[count($parts) - 1], '.yml');

            $data         = Yaml::parse($templatePath);
            $templateName = $data['name'];
            if (array_key_exists('rows', $data) && is_array($data['rows'])) {
                foreach ($data['rows'] as $row) {
                    if (is_array($row) && array_key_exists('regions', $row) && is_array($row['regions'])) {
                        foreach ($row['regions'] as $region) {
                            $contextTemplates[$region['name']][$fileName] = $templateName;
                        }
                    }
                }
            }
        }

        /*
            Example $contextTemplates contents:
            Array
            (
                [main] => Array
                    (
                        [full-width-page] => Full width page
                        [homepage] => Home page
                        [sidebar-page] => Page with left sidebar
                    )
                [top] => Array
                    (
                        [homepage] => Home page
                    )
                [sidebar] => Array
                    (
                        [homepage] => Home page
                        [sidebar-page] => Page with left sidebar
                    )
            )
        */

        // Link the page templates to the sections
        foreach ($sectionInfo as $fileName => $info) {
            $context = $info['context'];
            if (array_key_exists($context, $contextTemplates)) {
                $sectionInfo[$fileName]['pagetempates'] = $contextTemplates[$context];
            }
        }

        /*
            Example $sectionInfo contents:
            Array
            (
                [main] => Array
                    (
                        [context] => main
                        [pagetempates] => Array
                            (
                                [full-width-page] => Full width page
                                [homepage] => Home page
                                [sidebar-page] => Page with left sidebar
                            )

                    )

            )
        */

        $folder = $this->registry->getRepository('KunstmaanMediaBundle:Folder')->findOneBy(array('rel' => 'image'));
        $images = $this->registry->getRepository('KunstmaanMediaBundle:Media')->findBy(
            array('folder' => $folder, 'deleted' => false),
            array(),
            2
        );

        // Get all the available pages
        $finder = new Finder();
        $finder->files()->in($this->bundle->getPath() . '/Entity/Pages')->name('*.php');

        $pages = array();
        foreach ($finder as $pageFile) {
            $parts     = explode("/", $pageFile);
            $className = basename($parts[count($parts) - 1], '.php');

            $contents = file_get_contents($pageFile);
            if (strpos($contents, 'abstract class') === false && strpos($contents, 'interface ') === false) {
                $classNamespace = '\\' . $this->bundle->getNamespace() . '\Entity\Pages\\' . $className;
                $entity         = new $classNamespace;

                if (!method_exists($entity, 'getPagePartAdminConfigurations') || !method_exists(
                        $entity,
                        'getPageTemplates'
                    )
                ) {
                    continue;
                }

                $ppConfigs = $entity->getPagePartAdminConfigurations();
                $ptConfigs = $entity->getPageTemplates();

                foreach ($ppConfigs as $ppConfig) {
                    $parts            = explode(":", $ppConfig);
                    $ppConfigFilename = $parts[count($parts) - 1];

                    // Context found in this Page class
                    if (array_key_exists($ppConfigFilename, $sectionInfo)) {
                        // Search for templates
                        foreach ($ptConfigs as $ptConfig) {
                            $parts            = explode(":", $ptConfig);
                            $ptConfigFilename = $parts[count($parts) - 1];

                            // Page template found
                            if (array_key_exists($ptConfigFilename, $sectionInfo[$ppConfigFilename]['pagetempates'])) {
                                $formType = $entity->getDefaultAdminType();
                                if (!is_object($formType)) {
                                    $formType = $this->container->get($formType);
                                }

                                // Get all page properties
                                $form     = $this->container->get('form.factory')->create($formType);
                                $children = $form->createView()->children;

                                $pageFields = array();
                                foreach ($children as $field) {
                                    $name   = $field->vars['name'];
                                    $attr   = $field->vars['attr'];
                                    $blocks = $field->vars['block_prefixes'];

                                    if ($name == 'title' || $name == 'pageTitle') {
                                        continue;
                                    }

                                    if ($blocks[1] == 'hidden') {
                                        // do nothing
                                    } elseif ($blocks[1] == 'choice' && $blocks[1] == 'entity') {
                                        // do nothing
                                    } elseif ($blocks[1] == 'datetime') {
                                        $pageFields[]['datetime'] = array(
                                            'label'       => $this->labelCase($name),
                                            'date_random' => DateTime::date('d/m/Y'),
                                            'time_random' => DateTime::time('H:i')
                                        );
                                    } elseif ($blocks[1] == 'number') {
                                        $pageFields[]['decimal'] = array(
                                            'label'  => $this->labelCase($name),
                                            'random' => Base::randomFloat(2, 0, 99999)
                                        );
                                    } elseif ($blocks[1] == 'integer') {
                                        $pageFields[]['integer'] = array(
                                            'label'  => $this->labelCase($name),
                                            'random' => Base::randomNumber(3000, 99999)
                                        );
                                    } elseif ($blocks[1] == 'checkbox') {
                                        $pageFields[]['boolean'] = array(
                                            'label' => $this->labelCase($name)
                                        );
                                    } elseif ($blocks[1] == 'media') {
                                        $id                    = (count($images) > 0 ? $images[0]->getId() : 1);
                                        $pageFields[]['media'] = array(
                                            'label'  => $this->labelCase($name),
                                            'random' => $id
                                        );
                                    } elseif ($blocks[2] == 'urlchooser') {
                                        $pageFields[]['link'] = array(
                                            'label'  => $this->labelCase($name),
                                            'random' => 'http://www.' . strtolower(Lorem::word()) . '.com'
                                        );
                                    } elseif ($blocks[2] == 'textarea' && array_key_exists(
                                            'class',
                                            $attr
                                        ) && $attr['class'] == 'js-rich-editor rich-editor'
                                    ) {
                                        $pageFields[]['rich_text'] = array(
                                            'label'  => $this->labelCase($name),
                                            'random' => Lorem::sentence()
                                        );
                                    } elseif ($blocks[2] == 'textarea' || $blocks[1] == 'text') {
                                        $pageFields[]['text'] = array(
                                            'label'  => $this->labelCase($name),
                                            'random' => Lorem::word()
                                        );
                                    }
                                }

                                $pages[] = array(
                                    'name'     => $className,
                                    'section'  => $sectionInfo[$ppConfigFilename]['context'],
                                    'template' => $sectionInfo[$ppConfigFilename]['pagetempates'][$ptConfigFilename],
                                    'fields'   => $pageFields,
                                );
                            }
                        }
                    }
                }
            }
        }

        /*
            Example $pages contents:
            Array
            (
                [0] => Array
                    (
                        [name] => ContentPage
                        [section] => main
                        [template] => Page with left sidebar
                        [fields] => Array
                            (
                                ...
                            )
                    )
                [1] => Array
                    (
                        [name] => ContentPage
                        [section] => main
                        [template] => Full width page
                        [fields] => Array
                            (
                                ...
                            )
                    )
                [2] => Array
                    (
                        [name] => HomePage
                        [section] => main
                        [template] => Home page
                        [fields] => Array
                            (
                                ...
                            )
                    )
            )
        */

        // Add some random values in the field array, so that this values can be uses as test values
        foreach ($this->fields as $fkey => $fieldSet) {
            foreach ($fieldSet as $key => $values) {
                switch ($key) {
                    case 'multi_line':
                    case 'single_line':
                        $values[0]['random1'] = Lorem::word();
                        $values[0]['random2'] = Lorem::word();
                        $values[0]['lName']   = $this->labelCase($values[0]['fieldName']);
                        break;
                    case 'rich_text':
                        $values[0]['random1'] = Lorem::sentence();
                        $values[0]['random2'] = Lorem::sentence();
                        $values[0]['lName']   = $this->labelCase($values[0]['fieldName']);
                        break;
                    case 'link':
                        $values['url']['random1']      = 'http://www.' . strtolower(Lorem::word()) . '.com';
                        $values['url']['random2']      = 'http://www.' . strtolower(Lorem::word()) . '.com';
                        $values['url']['lName']        = $this->labelCase($values['url']['fieldName']);
                        $values['text']['random1']     = Lorem::word();
                        $values['text']['random2']     = Lorem::word();
                        $values['text']['lName']       = $this->labelCase($values['text']['fieldName']);
                        $values['new_window']['lName'] = $this->labelCase($values['new_window']['fieldName']);
                        break;
                    case 'image':
                        if (count($images) > 0) {
                            if (count($images) > 1) {
                                $values['image']['id_random1']  = $images[0]->getId();
                                $values['image']['url_random1'] = $images[0]->getUrl();
                                $values['image']['id_random2']  = $images[1]->getId();
                                $values['image']['url_random2'] = $images[1]->getUrl();
                            } else {
                                $values['image']['id_random1']  = $values['image']['id_random2'] = $images[0]->getId();
                                $values['image']['url_random1'] = $values['image']['url_random2'] = $images[0]->getUrl(
                                );
                            }
                        } else {
                            $values['image']['id_random1']  = $values['image']['id_random2'] = '1';
                            $values['image']['url_random1'] = $values['image']['url_random2'] = 'XXX';
                        }
                        $values['image']['lName']      = $this->labelCase($values['image']['fieldName']);
                        $values['alt_text']['random1'] = Lorem::word();
                        $values['alt_text']['random2'] = Lorem::word();
                        $values['alt_text']['lName']   = $this->labelCase($values['alt_text']['fieldName']);
                        break;
                    case 'boolean':
                        $values[0]['lName'] = $this->labelCase($values[0]['fieldName']);
                        break;
                    case 'integer':
                        $values[0]['random1'] = Base::randomNumber(3000, 99999);
                        $values[0]['random2'] = Base::randomNumber(3000, 99999);
                        $values[0]['lName']   = $this->labelCase($values[0]['fieldName']);
                        break;
                    case 'decimal':
                        $values[0]['random1'] = Base::randomFloat(2, 0, 99999);
                        $values[0]['random2'] = Base::randomFloat(2, 0, 99999);
                        $values[0]['lName']   = $this->labelCase($values[0]['fieldName']);
                        break;
                    case 'datetime':
                        $values[0]['date_random1']     = DateTime::date('d/m/Y');
                        $values[0]['date_random2']     = DateTime::date('d/m/Y');
                        $values[0]['time_random1']     = DateTime::time('H:i');
                        $values[0]['time_random2']     = DateTime::time('H:i');
                        $dparts                        = explode('/', $values[0]['date_random1']);
                        $values[0]['datetime_random1'] = $dparts[2] . '-' . $dparts[1] . '-' . $dparts[0] . ' ' . $values[0]['time_random1'] . ':00';
                        $dparts                        = explode('/', $values[0]['date_random2']);
                        $values[0]['datetime_random2'] = $dparts[2] . '-' . $dparts[1] . '-' . $dparts[0] . ' ' . $values[0]['time_random2'] . ':00';
                        $values[0]['lName']            = $this->labelCase($values[0]['fieldName']);
                        break;
                }

                $this->fields[$fkey][$key] = $values;
            }
        }

        $params = array(
            'name'   => $this->entity,
            'pages'  => $pages,
            'fields' => $this->fields
        );
        $this->renderFile(
            '/Features/PagePart.feature',
            $this->bundle->getPath() . '/Features/Admin' . $this->entity . '.feature',
            $params
        );

        $this->assistant->writeLine('Generating behat test : <info>OK</info>');
    }

    /**
     * Camel case string to space delimited string that will be used for form labels.
     *
     * @param string $text
     *
     * @return string
     */
    private function labelCase($text)
    {
        return ucfirst(str_replace('_', ' ', Container::underscore($text)));
    }

}
