<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Helper\CommandAssistant;
use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Finder\Finder;
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

        $welcomeText = $this->getWelcomeText();
        if (!empty($welcomeText)) {
            $this->assistant->writeSection($this->getWelcomeText());
        }

        $this->doInteract();
    }

    /**
     * @param InputInterface  $input
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
     * @param InputInterface  $input
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
    protected abstract function doInteract();

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
        $dir = dirname($this->getContainer()->getParameter('kernel.root_dir').'/').'/src/';
        $finder = new Finder();
        $finder->in($dir)->name('*Bundle.php');

        foreach ($finder as $file) {
            $bundles[$counter++] = array(
                'name'      => basename($file->getFilename(), '.php'),
                'namespace' => $file->getRelativePath(),
                'dir'       => $file->getPath()
            );
        }

        return $bundles;
    }

    /**
     * Check that a bundle is available (loaded in AppKernel)
     *
     * @param string $bundleName
     *
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
     * @param array  $text      What you want printed before the prefix is asked. If null is provided it'll write a default text.
     * @param string $namespace An optional namespace. If this is set it'll create the default based on this prefix.
     *                          If it's not provided it'll check if the InputInterface already has the namespace option.
     *
     * @return string The prefix. But it's also been set on the InputInterface.
     */
    protected function askForPrefix(array $text = null, $namespace = null)
    {
        $prefix = $this->assistant->getOptionOrDefault('prefix', null);

        if (is_null($text)) {
            $text = array(
                'You can add a prefix to the table names of the generated entities for example: ' .
                '<comment>projectname_bundlename_</comment>',
                'Enter an underscore \'_\' if you don\'t want a prefix.',
                ''
            );
        }

        while (is_null($prefix)) {
            if (count($text) > 0) {
                $this->assistant->writeLine($text);
            }

            if (is_null($namespace) || empty($namespace)) {
                $namespace = $this->assistant->getOption('namespace');
            } else {
                $namespace = $this->fixNamespace($namespace);
            }
            $defaultPrefix = GeneratorUtils::cleanPrefix($this->convertNamespaceToSnakeCase($namespace));
            $prefix        = GeneratorUtils::cleanPrefix($this->assistant->ask('Tablename prefix', $defaultPrefix));

            if($prefix == '') {
                break;
            }

            $output = $this->assistant->getOutput();
            if(!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $prefix)) {
                $output->writeln(sprintf('<bg=red> "%s" contains invalid characters</>', $prefix));
                $prefix = $text = null;
                continue;
            }

            $this->assistant->setOption('prefix', $prefix);
        }

        return $prefix;
    }

    /**
     * Converts something like Namespace\BundleNameBundle to namespace_bundlenamebundle.
     *
     * @param string $namespace
     *
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
     *
     * @return mixed
     */
    private function fixNamespace($namespace)
    {
        return str_replace('\\', '/', $namespace);
    }

    /**
     * Ask for which bundle we need to generate something. It there is only one custom bundle
     * created by the user, we don't ask anything and just use that bundle. If the user provided
     * a namespace as input option, we try to get that bundle first.
     *
     * @param string      $objectName The thing we are going to create (pagepart, bundle, layout, ...)
     * @param string|null $namespace  The namespace provided as input option
     *
     * @return BundleInterface
     */
    protected function askForBundleName($objectName, $namespace = null)
    {
        $ownBundles = $this->getOwnBundles();
        if (count($ownBundles) <= 0) {
            $this->assistant->writeError("Looks like you don't have created any bundles for your project...", true);
        }

        // If the user provided the namespace as input option
        if (!is_null($namespace)) {
            foreach ($ownBundles as $key => $bundleInfo) {
                if (GeneratorUtils::fixNamespace($namespace) == GeneratorUtils::fixNamespace(
                        $bundleInfo['namespace']
                    )
                ) {
                    $bundleName = $bundleInfo['name'];
                    break;
                }
            }

            // When the provided namespace was not found, we show an error on the screen and ask the bundle again
            if (empty($bundleName)) {
                $this->assistant->writeError("The provided bundle namespace '$namespace' was not found...");
            }
        }

        if (empty($bundleName)) {
            // If we only have 1 bundle, we don't need to ask
            if (count($ownBundles) > 1) {
                $bundleSelect = array();
                foreach ($ownBundles as $key => $bundleInfo) {
                    $bundleSelect[$key] = $bundleInfo['name'];
                }
                $bundleId   = $this->assistant->askSelect(
                    'In which bundle do you want to create the ' . $objectName,
                    $bundleSelect
                );
                $bundleName = $ownBundles[$bundleId]['name'];

                $this->assistant->writeLine('');
            } else {
                $bundleName = $ownBundles[1]['name'];
                $this->assistant->writeLine(
                    array("The $objectName will be created for the <comment>$bundleName</comment> bundle.\n")
                );
            }
        }

        $bundle = $this->assistant->getKernel()->getBundle($bundleName);

        return $bundle;
    }

    /**
     * Ask the end user to select one (or more) section configuration(s).
     *
     * @param string          $question
     * @param BundleInterface $bundle
     * @param bool            $multiple
     * @param string|null     $context
     * @param array           $defaultSections
     *
     * @return array|null
     */
    protected function askForSections(
        $question,
        BundleInterface $bundle,
        $multiple = false,
        $context = null,
        $defaultSections = array()
    ) {
        $allSections = $this->getAvailableSections($bundle, $context, $defaultSections);
        $sections    = array();

        // If there are more options to choose from, we ask the end user
        if (count($allSections) > 0) {
            $sectionSelect = array();
            foreach ($allSections as $key => $sectionInfo) {
                $sectionSelect[$key] = $sectionInfo['name'] . ' (' . $sectionInfo['file'] . ')';
            }
            $this->assistant->writeLine('');
            $sectionIds = $this->assistant->askSelect($question, $sectionSelect, null, $multiple);
            if (is_array($sectionIds)) {
                foreach ($sectionIds as $id) {
                    $sections[] = $allSections[$id]['file'];
                }
            } else {
                $sections[] = $allSections[$sectionIds]['file'];
            }
        }

        if ($multiple) {
            return $sections;
        } else {
            return count($sections) > 0 ? $sections[0] : null;
        }
    }

    /**
     * Get an array with the available page sections. We also parse the yaml files to get more information about
     * the sections.
     *
     * @param BundleInterface $bundle          The bundle for which we want to get the section configuration
     * @param string|null     $context         If provided, only return configurations with this context
     * @param array           $defaultSections The default section configurations that are always available
     *
     * @return array
     */
    protected function getAvailableSections(BundleInterface $bundle, $context = null, $defaultSections = array())
    {
        $configs = array();
        $counter = 1;

        // Get the available sections from disc
        $dir = $bundle->getPath() . '/Resources/config/pageparts/';
        if (file_exists($dir) && is_dir($dir)) {
            $finder = new Finder();
            $finder->files()->in($dir)->depth('== 0');
            foreach ($finder as $file) {
                $info = $this->getSectionInfo($dir, $file->getFileName());

                if (is_array($info) && (is_null($context) || $info['context'] == $context)) {
                    $configs[$counter++] = $info;
                    if (array_key_exists($info['file'], $defaultSections)) {
                        unset($defaultSections[$info['file']]);
                    }
                }

            }
        }

        // Add the default sections
        foreach ($defaultSections as $file => $info) {
            if (is_null($context) || $info['context'] == $context) {
                $configs[$counter++] = $info;
            }
        }

        return $configs;
    }

    /**
     * Get the information about a pagepart section configuration file.
     *
     * @param string $dir
     * @param string $file
     *
     * @return array|null
     */
    private function getSectionInfo($dir, $file)
    {
        $info = null;
        try {
            $data = Yaml::parse($dir . $file);
            $info = array(
                'name'    => $data['name'],
                'context' => $data['context'],
                'file'    => $file,
                //'file_clean' => substr($file, 0, strlen($file)-4)
            );
        } catch (ParseException $e) {
        }

        return $info;
    }

    /**
     * Get an array of fields that need to be added to the entity.
     *
     * @param BundleInterface $bundle
     * @param array           $reservedFields
     *
     * @return array
     */
    protected function askEntityFields(BundleInterface $bundle, array $reservedFields = array('id'))
    {
        $this->assistant->writeLine('<info>Available field types:</info> ');
        $typeSelect = $this->getTypes(true);
        foreach ($typeSelect as $type) {
            $this->assistant->writeLine(sprintf('<comment>- %s</comment>', $type));
        }

        $fields          = array();
        $self            = $this;
        $typeStrings     = $this->getTypes();
        $mediaTypeSelect = $this->getMediaTypes();
        $generator       = $this->getGenerator();
        $container       = $this->getContainer();

        while (true) {
            $this->assistant->writeLine('');

            $fieldName = $this->assistant->askAndValidate(
                'New field name (press <return> to stop adding fields)',
                function ($name) use ($fields, $self, $reservedFields, $generator, $container) {
                    // The fields cannot exist in the reserved field list
                    if (in_array($name, $reservedFields)) {
                        throw new \InvalidArgumentException(sprintf(
                            'Field "%s" is already defined in the parent class',
                            $name
                        ));
                    }

                    // The fields cannot exist already
                    if (isset($fields[$name])) {
                        throw new \InvalidArgumentException(sprintf('Field "%s" is already defined', $name));
                    }

                    // Check reserved words
                    if ($generator->isReservedKeyword($name)) {
                        throw new \InvalidArgumentException(sprintf('Name "%s" is a reserved word', $name));
                    }

                    // Only accept a-z
                    if (!preg_match('/^[a-zA-Z][a-zA-Z_0-9]+$/', $name) && $name != '') {
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
                $bundleName = $bundle->getName();
                $question   = "Reference entity name (eg. $bundleName:FaqItem, $bundleName:Blog/Comment)";
                $name       = $this->assistant->askAndValidate(
                    $question,
                    function ($name) use ($fields, $self, $bundle, $generator, $container) {
                        $parts = explode(':', $name);

                        // Should contain colon
                        if (count($parts) != 2) {
                            throw new \InvalidArgumentException(sprintf('"%s" is an invalid entity name', $name));
                        }

                        // Check reserved words
                        if ($generator->isReservedKeyword($parts[1])) {
                            throw new \InvalidArgumentException(sprintf('"%s" contains a reserved word', $name));
                        }

                        $em = $container->get('doctrine')->getEntityManager();
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

                $extra = $name;
            } else {
                $extra = null;
            }

            // If image type, force image media filter
            if ($typeStrings[$typeId] == 'image') {
                $extra = 'image';
            }

            // If media type, ask for media filter
            if ($typeStrings[$typeId] == 'media') {
                $mediaTypeId = $this->assistant->askSelect('Media filter', $mediaTypeSelect);
                $extra       = strtolower($mediaTypeSelect[$mediaTypeId]);
            }

            if ($typeStrings[$typeId] == 'image' || $typeStrings[$typeId] == 'media') {

                // Ask the allowed mimetypes for the media ojbect
                $mimeTypes = $this->assistant->ask ('Do you want to limit the possible file types? Then specify a comma-seperated list of types (example: image/png,image/svg+xml), otherwise press ENTER', null);
                if(isset($mimeTypes)) {
                    $mimeTypes = explode(',', $mimeTypes);
                }
                $data = array (
                        'name' => $fieldName,
                        'type' => $typeStrings [$typeId],
                        'extra' => $extra,
                        'mimeTypes' => $mimeTypes,
                );

                if ($extra == 'image') {

                    $minHeight = $maxHeight = $minWidth = $maxWidth = null;
                    if ($this->assistant->askConfirmation('Do you want to add validation of the dimensions of the media object? (y/n)', 'n', '?', false)) {

                        // Ask the minimum height allowed for the image
                        $lengthValidation = function ($length) {
                            if ((is_numeric($length) && $length < 0) || (!is_numeric($length) && !empty($length))) {
                                throw new \InvalidArgumentException(sprintf('"%s" is not a valid length', $length));
                            } else {
                                return $length;
                            }
                        };

                        $minHeight = $this->assistant->askAndValidate('What is the minimum height for the media object? (in pixels)', $lengthValidation);

                        // Ask the maximum height allowed for the image
                        $maxHeight = $this->assistant->askAndValidate('What is the maximum height for the media object? (in pixels)', $lengthValidation);

                        // Ask the minimum width allowed for the image
                        $minWidth = $this->assistant->askAndValidate('What is the minimum width for the media object? (in pixels)', $lengthValidation);

                        //Ask the maximum width allowed for the image
                        $maxWidth = $this->assistant->askAndValidate('What is the maximum width for the media object? (in pixels)', $lengthValidation);

                    }
                    $data = array('name' => $fieldName, 'type' => 'image', 'extra' => $extra,
                        'minHeight' => $minHeight, 'maxHeight' => $maxHeight, 'minWidth' => $minWidth, 'maxWidth' => $maxWidth, 'mimeTypes' => $mimeTypes);

                }
            } else $data = array('name' => $fieldName, 'type' => $typeStrings[$typeId], 'extra' => $extra);
            $fields[$fieldName] = $data;
        }

        return $fields;
    }

    /**
     * Get all the available types.
     *
     * @param bool $niceNames
     *
     * @return array
     */
    private function getTypes($niceNames = false)
    {
        $counter = 1;

        $types             = array();
        $types[$counter++] = $niceNames ? 'Single line text' : 'single_line';
        $types[$counter++] = $niceNames ? 'Multi line text' : 'multi_line';
        $types[$counter++] = $niceNames ? 'Wysiwyg' : 'wysiwyg';
        $types[$counter++] = $niceNames ? 'Link (url, text, new window)' : 'link';
        if ($this->isBundleAvailable('KunstmaanMediaPagePartBundle')) {
            $types[$counter++] = $niceNames ? 'Image (media, alt text)' : 'image';
            $types[$counter++] = $niceNames ? 'Media (File or Video or Slideshow)' : 'media';
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
     * Get all available media types.
     *
     * @return array
     */
    private function getMediaTypes()
    {
        $counter = 1;

        $types             = array();
        $types[$counter++] = 'None';
        $types[$counter++] = 'File';
        $types[$counter++] = 'Image';
        $types[$counter++] = 'Video';

        return $types;
    }

    /**
     * Get all the entity fields for a specific type.
     *
     * @param BundleInterface $bundle
     * @param                 $objectName
     * @param                 $prefix
     * @param                 $name
     * @param                 $type
     * @param string|null     $extra
     * @param bool            $allNullable
     *
     * @return array
     */
    protected function getEntityFields(
        BundleInterface $bundle,
        $objectName,
        $prefix,
        $name,
        $type,
        $extra = null,
        $minHeight = null,
        $maxHeight = null,
        $minWidth = null,
        $maxWidth = null,
        $mimeTypes = null,
        $allNullable = false
    ) {
        $fields = array();
        switch ($type) {
            case 'single_line':
                $fields[$type][] = array(
                    'fieldName' => lcfirst(Container::camelize($name)),
                    'type'      => 'string',
                    'length'    => '255',
                    'formType'  => 'text',
                    'nullable'  => $allNullable
                );
                break;
            case 'multi_line':
                $fields[$type][] = array(
                    'fieldName' => lcfirst(Container::camelize($name)),
                    'type'      => 'text',
                    'formType'  => 'textarea',
                    'nullable'  => $allNullable
                );
                break;
        case 'wysiwyg':
        $fields[$type][] = array(
            'fieldName' => lcfirst(Container::camelize($name)),
            'type'      => 'text',
            'formType'  => 'wysiwyg',
            'nullable'  => $allNullable
        );
        break;
            case 'link':
                foreach (array('url', 'text') as $subField) {
                    $fields[$type][$subField] = array(
                        'fieldName' => lcfirst(Container::camelize($name . '_' . $subField)),
                        'type'      => 'string',
                        'formType'  => $subField == 'url' ? 'urlchooser' : 'text',
                        'nullable'  => $allNullable
                    );
                }
                $fields[$type]['new_window'] = array(
                    'fieldName' => lcfirst(Container::camelize($name . '_new_window')),
                    'type'      => 'boolean',
                    'nullable'  => true,
                    'formType'  => 'checkbox'
                );
                break;
            case 'image':
                $fields[$type]['image']    = array(
                    'fieldName'    => lcfirst(Container::camelize($name)),
                    'type'         => 'image',
                    'formType'     => 'media',
                    'mediaType'    => $extra,
                    'minHeight'    => $minHeight,
                    'maxHeight'    => $maxHeight,
                    'minWidth'     => $minWidth,
                    'maxWidth'     => $maxWidth,
                    'mimeTypes'    => $mimeTypes,
                    'targetEntity' => 'Kunstmaan\MediaBundle\Entity\Media',
                    'joinColumn'   => array(
                        'name'                 => str_replace('.', '_', Container::underscore($name . '_id')),
                        'referencedColumnName' => 'id'
                    ),
                    'nullable'     => $allNullable
                );
                $fields[$type]['alt_text'] = array(
                    'fieldName' => lcfirst(Container::camelize($name . '_alt_text')),
                    'type'      => 'text',
                    'nullable'  => true,
                    'formType'  => 'text'
                );
                break;
            case 'media':
                $fields[$type][] = array(
                    'fieldName'    => lcfirst(Container::camelize($name)),
                    'type'         => 'media',
                    'formType'     => 'media',
                    'mediaType'    => $extra,
                    'mimeTypes'    => $mimeTypes,
                    'targetEntity' => 'Kunstmaan\MediaBundle\Entity\Media',
                    'joinColumn'   => array(
                        'name'                 => str_replace('.', '_', Container::underscore($name . '_id')),
                        'referencedColumnName' => 'id'
                    ),
                    'nullable'     => $allNullable
                );
                break;
            case 'single_ref':
                $em              = $this->getContainer()->get('doctrine')->getEntityManager();
                $entityName      = $em->getClassMetadata($extra)->name;
                $fields[$type][] = array(
                    'fieldName'    => lcfirst(Container::camelize($name)),
                    'type'         => 'entity',
                    'formType'     => 'entity',
                    'targetEntity' => $entityName,
                    'joinColumn'   => array(
                        'name'                 => str_replace('.', '_', Container::underscore($name . '_id')),
                        'referencedColumnName' => 'id'
                    ),
                    'nullable'     => $allNullable
                );
                break;
            case 'multi_ref':
                $em              = $this->getContainer()->get('doctrine')->getEntityManager();
                $entityName      = $em->getClassMetadata($extra)->name;
                $parts           = explode("\\", $entityName);
                $joinTableName   = strtolower(
                    $prefix . Container::underscore($objectName) . '_' . Container::underscore(
                        $parts[count($parts) - 1]
                    )
                );
                $fields[$type][] = array(
                    'fieldName'    => lcfirst(Container::camelize($name)),
                    'type'         => 'entity',
                    'formType'     => 'entity',
                    'targetEntity' => $entityName,
                    'joinTable'    => array(
                        'name'               => $joinTableName,
                        'joinColumns'        => array(
                            array(
                                'name'                 => strtolower(Container::underscore($objectName)) . '_id',
                                'referencedColumnName' => 'id'
                            )
                        ),
                        'inverseJoinColumns' => array(
                            array(
                                'name'                 => strtolower(
                                        Container::underscore($parts[count($parts) - 1])
                                    ) . '_id',
                                'referencedColumnName' => 'id',
                                'unique'               => true
                            )
                        )
                    ),
                    'nullable'     => $allNullable
                );
                break;
            case 'boolean':
                $fields[$type][] = array(
                    'fieldName' => lcfirst(Container::camelize($name)),
                    'type'      => 'boolean',
                    'formType'  => 'checkbox',
                    'nullable'  => $allNullable
                );
                break;
            case 'integer':
                $fields[$type][] = array(
                    'fieldName' => lcfirst(Container::camelize($name)),
                    'type'      => 'integer',
                    'formType'  => 'integer',
                    'nullable'  => $allNullable
                );
                break;
            case 'decimal':
                $fields[$type][] = array(
                    'fieldName' => lcfirst(Container::camelize($name)),
                    'type'      => 'decimal',
                    'precision' => 10,
                    'scale'     => 2,
                    'formType'  => 'number',
                    'nullable'  => $allNullable
                );
                break;
            case 'datetime':
                $fields[$type][] = array(
                    'fieldName' => lcfirst(Container::camelize($name)),
                    'type'      => 'datetime',
                    'formType'  => 'datetime',
                    'nullable'  => $allNullable
                );
                break;
        }

        return $fields;
    }

    /**
     * Get an array with the available page templates.
     *
     * @param BundleInterface $bundle The bundle for which we want to get the template configurations
     *
     * @return array
     */
    protected function getAvailableTemplates(BundleInterface $bundle)
    {
        $configs = array();
        $counter = 1;

        // Get the available sections from disc
        $dir = $bundle->getPath() . '/Resources/config/pagetemplates/';
        if (file_exists($dir) && is_dir($dir)) {
            $finder = new Finder();
            $finder->files()->in($dir)->depth('== 0');
            foreach ($finder as $file) {
                $info = $this->getTemplateInfo($dir, $file->getFileName());
                if (is_array($info)) {
                    $configs[$counter++] = $info;
                }
            }
        }

        return $configs;
    }

    /**
     * Get the information about a pagepart section configuration file.
     *
     * @param string $dir
     * @param string $file
     *
     * @return array|null
     */
    protected function getTemplateInfo($dir, $file)
    {
        $info = null;
        try {
            $data = Yaml::parse($dir . $file);
            // Parse contexts
            $contexts = array();
            foreach ($data['rows'] as $row) {
                foreach ($row['regions'] as $region) {
                    $contexts[] = $region['name'];
                }
            }
            $info = array(
                'name'     => $data['name'],
                'contexts' => $contexts,
                'file'     => $file,
            );
        } catch (ParseException $e) {
        }

        return $info;
    }

    /**
     * Get an array with the available page templates.
     *
     * @param BundleInterface $bundle The bundle for which we want to get the template configurations
     *
     * @return array
     */
    protected function getAvailablePages(BundleInterface $bundle)
    {
        $pages = array();
        $counter = 1;

        // Get the available pages from disc
        $dir = $bundle->getPath() . '/Entity/Pages/';
        if (file_exists($dir) && is_dir($dir)) {
            $finder = new Finder();
            $finder->files()->in($dir)->depth('== 0');
            foreach ($finder as $file) {
                $pages[$counter++] = array(
                    'name' => substr($file->getFileName(), 0, strlen($file->getFileName())-4),
                    'path' => $file->getPathName()
                );
            }
        }

        return $pages;
    }

    /**
     * Check that it is possible to generate the behat tests.
     *
     * @param BundleInterface $bundle
     *
     * @return bool
     */
    protected function canGenerateBehatTests(BundleInterface $bundle)
    {
    $behatFile = dirname($this->getContainer()->getParameter('kernel.root_dir').'/') . '/behat.yml';
    $pagePartContext = $bundle->getPath() . '/Features/Context/PagePartContext.php';
    $behatTestPage = $bundle->getPath() . '/Entity/Pages/BehatTestPage.php';

    // Make sure behat is configured and the PagePartContext and BehatTestPage exits
    return (file_exists($behatFile) && file_exists($pagePartContext) && file_exists($behatTestPage));
    }
}
