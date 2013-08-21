<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Generator\PagePartGenerator;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Sensio\Bundle\GeneratorBundle\Generator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Generates a new pagepart
 */
class GeneratePagePartCommand extends KunstmaanGenerateCommand
{
    /**
     * @var string
     */
    private $bundleName;

    /**
     * @var string
     */
    private $pagepartName;

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
     * @see Command
     */
    protected function configure()
    {
        $this->setDescription('Generates a new pagepart')
            ->setHelp(<<<EOT
The <info>kuma:generate:pagepart</info> command generates a new pagepart and the pagepart configuration.

<info>php app/console kuma:generate:pagepart</info>
EOT
            )
            ->addOption('prefix', '', InputOption::VALUE_OPTIONAL, 'The prefix to be used in the table names of the generated entities')
            ->setName('kuma:generate:pagepart');
    }

    /**
     * {@inheritdoc}
     */
    protected function getWelcomeText()
    {
        return 'Welcome to the Kunstmaan pagepart generator';
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute()
    {
        $this->assistant->writeSection('PagePart generation');

        $bundle = $this->assistant->getKernel()->getBundle($this->bundleName);
        $fields = array();
        foreach ($this->fields as $fieldInfo) {
            $fields[] = $this->getEntityFields($fieldInfo['name'], $fieldInfo['type'], $fieldInfo['extra']);
        }

        $this->createGenerator()->generate($bundle, $this->pagepartName, $this->prefix, $fields, $this->sections);

        $this->assistant->writeSection('PagePart successfully created', 'bg=green;fg=black');
        $this->assistant->writeLine(array(
            'Make sure you update your database first before you test the pagepart:',
            '    Directly update your database:          <comment>app/console doctrine:schema:update --force</comment>',
            '    Create a Doctrine migration and run it: <comment>app/console doctrine:migrations:diff && app/console doctrine:migrations:migrate</comment>',
            ''
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function doInteract()
    {
        if (!$this->isBundleAvailable('KunstmaanPagePartBundle')) {
            $this->assistant->writeError('KunstmaanPagePartBundle not found', true);
        }

        $this->assistant->writeLine(array("This command helps you to generate a new pagepart.\n"));

        /**
         * Ask for which bundle we need to create the pagepart
         */
        $ownBundles = $this->getOwnBundles();
        if (count($ownBundles) <= 0) {
            $this->assistant->writeError("Looks like you don't have created a bundle for your project, create one first.", true);
        }

        // If we only have 1 bundle, we don't need to ask
        if (count($ownBundles) > 1) {
            $bundleSelect = array();
            foreach ($ownBundles as $key => $bundleInfo) {
                $bundleSelect[$key] = $bundleInfo['namespace'].':'.$bundleInfo['name'];
            }
            $bundleId = $this->assistant->askSelect('In which bundle do you want to create the pagepart', $bundleSelect);
            $this->bundleName = $ownBundles[$bundleId]['namespace'].$ownBundles[$bundleId]['name'];

            $namespace = $ownBundles[$bundleId]['namespace'].'/'.$ownBundles[$bundleId]['name'];

            $this->assistant->writeLine('');
        } else {
            $this->bundleName = $ownBundles[1]['namespace'].$ownBundles[1]['name'];
            $this->assistant->writeLine(array("The pagepart will be created for the <comment>".$this->bundleName."</comment> bundle.\n"));

            $namespace = $ownBundles[1]['namespace'].'/'.$ownBundles[1]['name'];
        }

        /**
         * Ask the prefix for the database
         */
        $this->prefix = $this->askForPrefix(null, $namespace);

        /**
         * Ask the name of the pagepart
         */
        $this->assistant->writeLine(array(
            '',
            'The name of your PagePart: For example: <comment>ContentBoxPagePart</comment>',
            '',
        ));
         while (true) {
            $name = $this->assistant->ask('PagePart name');
            try {
                // Check reserved words
                if ($this->getGenerator()->isReservedKeyword($name)){
                    $this->assistant->writeError(sprintf('"%s" is a reserved word', $name));
                    continue;
                }

                // Name should end on PagePart
                if (!preg_match('/PagePart$/', $name)) {
                    $this->assistant->writeError('The pagepart name must end with PagePart');
                    continue;
                }

                // Name should contain more characters than PagePart
                if (strlen($name) <= strlen('PagePart') || !preg_match('/^[a-zA-Z]+$/', $name)) {
                    $this->assistant->writeError('Invalid pagepart name');
                    continue;
                }

                // Check that entity does not already exist
                $bundle = $this->getApplication()->getKernel()->getBundle($this->bundleName);
                if (file_exists($bundle->getPath().'/Entity/PageParts/'.$name.'.php')) {
                    $this->assistant->writeError(sprintf('PagePart or entity "%s" already exists', $name));
                    continue;
                }

                // If we get here, the name is valid
                break;
            } catch (\Exception $e) {
                $this->assistant->writeError(sprintf('Bundle "%s" does not exist', $this->bundleName));
            }
        }
        $this->pagepartName = $name;

        /**
         * Ask which fields need to be present
         */
        $this->fields = $this->addFields();

        /**
         * Ask for which page sections we should enable this pagepart
         */
        $bundle = $this->assistant->getKernel()->getBundle($this->bundleName);
        $allSections = $this->getAvailableSections($bundle);
        $this->sections = array();

        if (count($allSections) > 0) {
            $sectionSelect = array();
            foreach ($allSections as $key => $sectionInfo) {
                $sectionSelect[$key] = $sectionInfo['name'];
            }
            $this->assistant->writeLine('');
            $sectionIds = $this->assistant->askSelect('In which page section configuration file(s) do you want to add the pagepart (multiple possible, separated by comma)', $sectionSelect, null, true);
            foreach ($sectionIds as $id) {
                $this->sections[] = $allSections[$id]['file'];
            }
        }
    }

    /**
     * @return array
     * @throws \InvalidArgumentException
     */
    private function addFields()
    {
        $this->assistant->writeLine(array("\nInstead of starting with a blank pagepart, you can add some fields now.\n"));

        $this->assistant->writeLine('<info>Available field types:</info> ');
        $typeSelect = $this->getTypes(true);
        foreach ($typeSelect as $type) {
            $this->assistant->writeLine(sprintf('<comment>- %s</comment>', $type));
        }

        $fields = array();
        $self = $this;
        $typeStrings = $this->getTypes();

        while (true) {
            $this->assistant->writeLine('');

            $fieldName = $this->assistant->askAndValidate(
                'New field name (press <return> to stop adding fields)',
                function ($name) use ($fields, $self) {
                    // The fields cannot exist already
                    if (isset($fields[$name]) || 'id' == $name) {
                        throw new \InvalidArgumentException(sprintf('Field "%s" is already defined', $name));
                    }

                    // Check reserved words
                    if ($self->getGenerator()->isReservedKeyword($name)) {
                        throw new \InvalidArgumentException(sprintf('Name "%s" is a reserved word', $name));
                    }

                    // Only accept a-z
                    if (!preg_match('/^[a-zA-Z_]+$/', $name) && $name != '') {
                        throw new \InvalidArgumentException(sprintf('Name "%s" is invalid', $name));
                    }

                    return $name;
                }
            );

            // When <return> is entered
            if (!$fieldName) {
                break;
            }

            $typeId = $this->assistant->askSelect('Field type', $typeSelect);
            // If single -or multipe entity reference in chosen, we need to ask for the entity name
            if (in_array($typeStrings[$typeId], array('single_ref', 'multi_ref'))) {
                while (true) {
                    $name = $this->assistant->ask('Reference entity name (eg. FaqItem, Blog\Comment)');
                    try {
                        // Check reserved words
                        if ($this->getGenerator()->isReservedKeyword($name)){
                            $this->assistant->writeError(sprintf('"%s" is a reserved word', $name));
                            continue;
                        }

                        // Check that entity does not already exist
                        $bundle = $this->assistant->getKernel()->getBundle($this->bundleName);
                        $path = $bundle->getPath().'/Entity/'.str_replace('\\', '/', $name).'.php';
                        if (!file_exists($path)) {
                            $this->assistant->writeError(sprintf('Entity "%s" not found on this path "%s"', $name, $path));
                            continue;
                        }

                        // If we get here, the name is valid
                        break;
                    } catch (\Exception $e) {
                        $this->assistant->writeError(sprintf('Bundle "%s" does not exist', $this->bundleName));
                    }
                }
                $extra = $name;
            } else {
                $extra = null;
            }

            $data = array('name' => $fieldName, 'type' => $typeStrings[$typeId], 'extra' => $extra);
            $fields[$fieldName] = $data;
        }

        return $fields;
    }

    /**
     * Get the generator.
     *
     * @return PagePartGenerator
     */
    protected function createGenerator()
    {
        $filesystem = $this->getContainer()->get('filesystem');
        $registry = $this->getContainer()->get('doctrine');
        return new PagePartGenerator($filesystem, $registry, '/pagepart', $this->assistant);
    }

    /**
     * Get an array with the available page sections.
     *
     * @param BundleInterface $bundle
     * @return array
     */
    private function getAvailableSections(BundleInterface $bundle) {
        $configs = array();
        $counter = 1;

        $dir = $bundle->getPath().'/Resources/config/pageparts/';
        if (file_exists($dir) && is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if (is_file($dir.$file) && !in_array($file, array('.', '..')) && substr($file, -4) == '.yml') {
                    $configs[$counter++] = array(
                        'file' => $file,
                        'name' => substr($file, 0, strlen($file)-4)
                    );
                }
            }
        }

        return $configs;
    }

    /**
     * Get all the available types.
     *
     * @param bool $niceNames
     * @return array
     */
    private function getTypes($niceNames = false)
    {
        $counter = 1;

        $types = array();
        $types[$counter++] = $niceNames ? 'Single line text' : 'single_line';
        $types[$counter++] = $niceNames ? 'Multi line text' : 'multi_line';
        $types[$counter++] = $niceNames ? 'Rich text' : 'rich_text';
        $types[$counter++] = $niceNames ? 'Link (url, text, new window)' : 'link';
        if ($this->isBundleAvailable('KunstmaanMediaPagePartBundle')) {
            $types[$counter++] = $niceNames ? 'Image (media, alt text)' : 'image';
        }
        $types[$counter++] = $niceNames ? 'Single entity reference' : 'single_ref';
        $types[$counter++] = $niceNames ? 'Multi entity reference' : 'multi_ref';
        $types[$counter++] = $niceNames ? 'Boolean' : 'boolean';
        $types[$counter++] = $niceNames ? 'Integer' : 'integer';
        $types[$counter++] = $niceNames ? 'Decimal number' : 'decimal';
        $types[$counter++] = $niceNames ? 'DateTime' : 'datetime';

        return $types;
    }

    /**
     * Get all the entity fields for a specific type.
     *
     * @param string $name
     * @param string $type
     * @param string|null $extra
     * @return array
     */
    private function getEntityFields($name, $type, $extra)
    {
        $fields = array();
        switch ($type) {
            case 'single_line':
                $fields[$type][] = array(
                    'fieldName' => lcfirst(Container::camelize($name)),
                    'type' => 'string',
                    'length' => '255',
                    'formType' => 'text'
                );
                break;
            case 'multi_line':
            case 'rich_text':
                $fields[$type][] = array(
                    'fieldName' => lcfirst(Container::camelize($name)),
                    'type' => 'text',
                    'formType' => 'textarea'
                );
                break;
            case 'link':
                foreach (array('url', 'text') as $subField) {
                    $fields[$type][$subField] = array(
                        'fieldName' => lcfirst(Container::camelize($name.'_'.$subField)),
                        'type' => 'string',
                        'formType' => $subField == 'url' ? 'urlchooser' : 'text'
                    );
                }
                $fields[$type]['new_window'] = array(
                    'fieldName' => lcfirst(Container::camelize($name.'_new_window')),
                    'type' => 'boolean',
                    'nullable' => true,
                    'formType' => 'checkbox'
                );
                break;
            case 'image':
                $fields[$type]['image'] = array(
                    'fieldName' => lcfirst(Container::camelize($name.'_image')),
                    'type' => 'image',
                    'formType' => 'media',
                    'targetEntity' => 'Kunstmaan\MediaBundle\Entity\Media',
                    'joinColumn' => array(
                        'name' => str_replace('.', '_', Container::underscore($name.'_image_id')),
                        'referencedColumnName' => 'id'
                    )
                );
                $fields[$type]['alt_text'] = array(
                    'fieldName' => lcfirst(Container::camelize($name.'_alt_text')),
                    'type' => 'text',
                    'nullable' => true,
                    'formType' => 'text'
                );
                break;
            case 'single_ref':
                $em = $this->getContainer()->get('doctrine')->getEntityManager();
                $entityName = $em->getClassMetadata($this->bundleName.':'.$extra)->name;
                $fields[$type][] = array(
                    'fieldName' => lcfirst(Container::camelize($name)),
                    'type' =>  'entity',
                    'formType' => 'entity',
                    'targetEntity' => $entityName,
                    'joinColumn' => array(
                        'name' => str_replace('.', '_', Container::underscore($name.'_id')),
                        'referencedColumnName' => 'id'
                    )
                );
                break;
            case 'multi_ref':
                $em = $this->getContainer()->get('doctrine')->getEntityManager();
                $entityName = $em->getClassMetadata($this->bundleName.':'.$extra)->name;
                $bundle = $this->getContainer()->get('kernel')->getBundle($this->bundleName);
                list($project, $tmp) = explode("\\", $bundle->getNameSpace());
                $parts = explode("\\", $entityName);
                $joinTableName = strtolower($project.'_'.$this->pagepartName.'_'.$parts[count($parts)-1]);
                $fields[$type][] = array(
                    'fieldName' => lcfirst(Container::camelize($name)),
                    'type' => 'entity',
                    'formType' => 'entity',
                    'targetEntity' => $entityName,
                    'joinTable' => array(
                        'name' => $joinTableName,
                        'joinColumns' => array(array(
                            'name' => strtolower($this->pagepartName).'_id',
                            'referencedColumnName' => 'id'
                        )),
                        'inverseJoinColumns' => array(array(
                            'name' => strtolower($parts[count($parts)-1]).'_id',
                            'referencedColumnName' => 'id',
                            'unique' => true
                        ))
                    )
                );
                break;
            case 'boolean':
                $fields[$type][] = array(
                    'fieldName' => lcfirst(Container::camelize($name)),
                    'type' => 'boolean',
                    'formType' => 'checkbox'
                );
                break;
            case 'integer':
                $fields[$type][] = array(
                    'fieldName' => lcfirst(Container::camelize($name)),
                    'type' => 'integer',
                    'formType' => 'integer'
                );
                break;
            case 'decimal':
                $fields[$type][] = array(
                    'fieldName' => lcfirst(Container::camelize($name)),
                    'type' => 'decimal',
                    'precision' => 10,
                    'scale' => 2,
                    'formType' => 'number'
                );
                break;
            case 'datetime':
                $fields[$type][] = array(
                    'fieldName' => lcfirst(Container::camelize($name)),
                    'type' => 'datetime',
                    'formType' => 'datetime'
                );
                break;
        }

        return $fields;
    }
}
