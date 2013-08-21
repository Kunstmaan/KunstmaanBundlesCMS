<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Helper\CommandAssistant;
use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCommand;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

abstract class KunstmaanGenerateCommand extends GenerateDoctrineCommand
{
    /**
     * @var CommandAssistant
     */
    protected $assistant;

    /**
     * Interacts with the user.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->setInputAndOutput($input, $output);

        $this->assistant->writeSection($this->getWelcomeText());

        $this->doInteract();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setInputAndOutput($input, $output);

        return $this->doExecute();
    }

    /**
     * Create the CommandAssistant.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function setInputAndOutput(InputInterface $input, OutputInterface $output)
    {
        if (is_null($this->assistant)) {
            $this->assistant = new CommandAssistant();
            $this->assistant->setDialog($this->getDialogHelper());
            $this->assistant->setKernel($this->getApplication()->getKernel());
        }
        $this->assistant->setOutput($output);
        $this->assistant->setInput($input);
    }

    /**
     * Do the interaction with the end user.
     */
    abstract protected function doInteract();

    /**
     * This function implements the final execution of the Generator.
     * It calls the execute function with the correct parameters.
     */
    protected abstract function doExecute();

    /**
     * The text to be displayed on top of the generator.
     *
     * @return string|array
     */
    protected abstract function getWelcomeText();

    /**
     * Get an array with all the bundles the user has created.
     *
     * @return array
     */
    protected function getOwnBundles()
    {
        $bundles = array();
        $counter = 1;

        $dir = dirname($this->getContainer()->getParameter('kernel.root_dir')).'/src/';
        $files = scandir($dir);
        foreach ($files as $file) {
            if (is_dir($dir.$file) && !in_array($file, array('.', '..'))) {
                $bundleFiles = scandir($dir.$file);
                foreach ($bundleFiles as $bundleFile) {
                    if (is_dir($dir.$file.'/'.$bundleFile) && !in_array($bundleFile, array('.', '..'))) {
                        $bundles[$counter++] = array(
                            'name' => $bundleFile,
                            'namespace' => $file,
                            'dir' => $dir.$file.'/'.$bundleFile
                        );
                    }
                }
            }
        }

        return $bundles;
    }

    /**
     * Check that a bundle is available (loaded in AppKernel)
     *
     * @param string $bundleName
     * @return bool
     */
    protected function isBundleAvailable($bundleName)
    {
        $allBundles = array_keys($this->assistant->getKernel()->getBundles());
        return in_array($bundleName, $allBundles);
    }

    /**
     * Asks for the prefix and sets it on the InputInterface as the 'prefix' option, if this option is not set yet.
     * Will set the default to a snake_cased namespace when the namespace has been set on the InputInterface.
     *
     * @param array  $text What you want printed before the prefix is asked. If null is provided it'll write a default text.
     * @param string $namespace An optional namespace. If this is set it'll create the default based on this prefix.
     *  If it's not provided it'll check if the InputInterface already has the namespace option.
     *
     * @return string The prefix. But it's also been set on the InputInterface.
     */
    protected function askForPrefix(array $text = null, $namespace = null)
    {
        $prefix = $this->assistant->getOptionOrDefault('prefix', null);

        if (is_null($text)) {
            $text = array(
                '',
                'You can add a prefix to the table names of the generated entities for example: '.
                '<comment>projectname_bundlename_</comment>',
                'Enter an underscore \'_\' if you don\'t want a prefix.',
                ''
            );
        }

        if (is_null($prefix)) {
            if (count($text) > 0) {
                $this->assistant->writeLine($text);
            }

            if (is_null($namespace) || empty($namespace)) {
                $namespace = $this->assistant->getOption('namespace');
            } else {
                $namespace = $this->fixNamespace($namespace);
            }
            $defaultPrefix = GeneratorUtils::cleanPrefix($this->convertNamespaceToSnakeCase($namespace));
            $prefix = GeneratorUtils::cleanPrefix($this->assistant->ask('Tablename prefix', $defaultPrefix));
            $this->assistant->setOption('prefix', $prefix);
        }

        return $prefix;
    }

    /**
     * Converts something like Namespace\BundleNameBundle to namspace_bundlenamebundle.
     *
     * @param string $namespace
     * @return string
     */
    private function convertNamespaceToSnakeCase($namespace)
    {
        if (is_null($namespace)) {
            return null;
        }

        return str_replace('/', '_', strtolower($this->fixNamespace($namespace)));
    }

    /**
     * Replaces '\' with '/'.
     *
     * @param $namespace
     * @return mixed
     */
    private function fixNamespace($namespace)
    {
        return str_replace('\\', '/', $namespace);
    }

    /**
     * Ask for which bundle we need to generate something. It there is only one custome bundle
     * created by the user, we don't ask anything and just use that bundle.
     *
     * @param string $objectName
     * @return BundleInterface
     */
    protected function askForBundleName($objectName)
    {
        $ownBundles = $this->getOwnBundles();
        if (count($ownBundles) <= 0) {
            $this->assistant->writeError("Looks like you don't have created any bundles for your project...", true);
        }

        // If we only have 1 bundle, we don't need to ask
        if (count($ownBundles) > 1) {
            $bundleSelect = array();
            foreach ($ownBundles as $key => $bundleInfo) {
                $bundleSelect[$key] = $bundleInfo['namespace'].':'.$bundleInfo['name'];
            }
            $bundleId = $this->assistant->askSelect('In which bundle do you want to create the '.$objectName, $bundleSelect);
            $bundleName = $ownBundles[$bundleId]['namespace'].$ownBundles[$bundleId]['name'];

            $this->assistant->writeLine('');
        } else {
            $bundleName = $ownBundles[1]['namespace'].$ownBundles[1]['name'];
            $this->assistant->writeLine(array("The $objectName will be created for the <comment>$bundleName</comment> bundle.\n"));
        }
        $bundle = $this->assistant->getKernel()->getBundle($bundleName);

        return $bundle;
    }

    /**
     * Ask the end user to select one (or more) section configuration(s).
     *
     * @param string $question
     * @param BundleInterface $bundle
     * @param bool $multiple
     * @param string|null $context
     * @return array
     */
    protected function askForSections($question, BundleInterface $bundle, $multiple = false, $context = null)
    {
        $allSections = $this->getAvailableSections($bundle, $context);
        $sections = array();

        if (count($allSections) > 0) {
            $sectionSelect = array();
            foreach ($allSections as $key => $sectionInfo) {
                $sectionSelect[$key] = $sectionInfo['name'].' ('.$sectionInfo['file'].')';
            }
            $this->assistant->writeLine('');
            $sectionIds = $this->assistant->askSelect($question, $sectionSelect, null, $multiple);
            foreach ($sectionIds as $id) {
                $sections[] = $allSections[$id]['file'];
            }
        }

        return $sections;
    }

    /**
     * Get an array with the available page sections. We also parse the yaml files to get more information about
     * the sections.
     *
     * @param BundleInterface $bundle  The bundle for which we want to get the section configuration
     * @param string|null     $context If provided, only return configurations with this context
     * @return array
     */
    protected function getAvailableSections(BundleInterface $bundle, $context = null) {
        $configs = array();
        $counter = 1;

        $dir = $bundle->getPath().'/Resources/config/pageparts/';
        if (file_exists($dir) && is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if (is_file($dir.$file) && !in_array($file, array('.', '..')) && substr($file, -4) == '.yml') {
                    $info = $this->getSectionInfo($dir, $file);
                    if (is_array($info) && (is_null($context) || $info['context'] == $context)) {
                        $configs[$counter++] = $info;
                    }
                }
            }
        }

        return $configs;
    }

    /**
     * Get the information about a pagepart section configuration file.
     *
     * @param string $dir
     * @param string$file
     * @return array|null
     */
    private function getSectionInfo($dir, $file)
    {
        $info = null;
        try {
            $data = Yaml::parse($dir.$file);
            $info = array(
                'name' => $data['name'],
                'context' => $data['name'],
                'file' => $file,
                //'file_clean' => substr($file, 0, strlen($file)-4)
            );
        } catch (ParseException $e) {}

        return $info;
    }

    /**
     * Get an array of fields that need to be added to the entity.
     *
     * @param BundleInterface $bundle
     * @param array $reservedFields
     * @return array
     */
    protected function askEntityFields(BundleInterface $bundle, array $reservedFields = array('id'))
    {
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
                function ($name) use ($fields, $self, $reservedFields) {
                    // The fields cannot exist in the reserved field list
                    if (in_array($name, $reservedFields)) {
                        throw new \InvalidArgumentException(sprintf('Field "%s" is already defined in the parent class', $name));
                    }

                    // The fields cannot exist already
                    if (isset($fields[$name])) {
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
                    $bundleName = $bundle->getName();
                    $question = "Reference entity name (eg. $bundleName:FaqItem, $bundleName:Blog/Comment)";
                    $name = $this->assistant->askAndValidate(
                        $question,
                        function ($name) use ($fields, $self, $bundle) {
                            $parts = explode(':', $name);

                            // Should contain colon
                            if (count($parts) != 2) {
                                throw new \InvalidArgumentException(sprintf('"%s" is an invalid entity name', $name));
                            }

                            // Check reserved words
                            if ($self->getGenerator()->isReservedKeyword($parts[1])){
                                throw new \InvalidArgumentException(sprintf('"%s" contains a reserved word', $name));
                            }

                            $em = $self->getContainer()->get('doctrine')->getEntityManager();
                            try {
                                $em->getClassMetadata($name);
                            } catch (\Exception $e) {
                                throw new \InvalidArgumentException(sprintf('Entity "%s" not found', $name));
                            }

                            return $name;
                        },
                        null,
                        array($bundleName)
                    );

                    // If we get here, the name is valid
                    break;
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
     * Get all the available types.
     *
     * @param bool $niceNames
     * @return array
     */
    private function getTypes($niceNames = false)
    {
        $counter = 1;

        $types = array();
        $types[$counter++] = $niceNames ? 'Single line text'             : 'single_line';
        $types[$counter++] = $niceNames ? 'Multi line text'              : 'multi_line';
        $types[$counter++] = $niceNames ? 'Rich text'                    : 'rich_text';
        $types[$counter++] = $niceNames ? 'Link (url, text, new window)' : 'link';
        if ($this->isBundleAvailable('KunstmaanMediaPagePartBundle')) {
            $types[$counter++] = $niceNames ? 'Image (media, alt text)'  : 'image';
        }
        $types[$counter++] = $niceNames ? 'Single entity reference'      : 'single_ref';
        $types[$counter++] = $niceNames ? 'Multi entity reference'       : 'multi_ref';
        $types[$counter++] = $niceNames ? 'Boolean'                      : 'boolean';
        $types[$counter++] = $niceNames ? 'Integer'                      : 'integer';
        $types[$counter++] = $niceNames ? 'Decimal number'               : 'decimal';
        $types[$counter++] = $niceNames ? 'DateTime'                     : 'datetime';

        return $types;
    }

    /**
     * Get all the entity fields for a specific type.
     *
     * @param BundleInterface $bundle
     * @param $objectName
     * @param $prefix
     * @param $name
     * @param $type
     * @param null $extra
     * @return array
     */
    protected function getEntityFields(BundleInterface $bundle, $objectName, $prefix, $name, $type, $extra = null)
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
                $entityName = $em->getClassMetadata($extra)->name;
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
                $entityName = $em->getClassMetadata($extra)->name;
                $parts = explode("\\", $entityName);
                $joinTableName = strtolower($prefix.Container::underscore($objectName).'_'.Container::underscore($parts[count($parts)-1]));
                $fields[$type][] = array(
                    'fieldName' => lcfirst(Container::camelize($name)),
                    'type' => 'entity',
                    'formType' => 'entity',
                    'targetEntity' => $entityName,
                    'joinTable' => array(
                        'name' => $joinTableName,
                        'joinColumns' => array(array(
                            'name' => strtolower(Container::underscore($objectName)).'_id',
                            'referencedColumnName' => 'id'
                        )),
                        'inverseJoinColumns' => array(array(
                            'name' => strtolower(Container::underscore($parts[count($parts)-1])).'_id',
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