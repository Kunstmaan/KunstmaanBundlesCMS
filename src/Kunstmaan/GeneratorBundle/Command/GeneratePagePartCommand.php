<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Generator\PagePartGenerator;
use Symfony\Component\Console\Input\InputOption;

/**
 * Generates a new pagepart
 */
class GeneratePagePartCommand extends KunstmaanGenerateCommand
{
    /**
     * @var BundleInterface
     */
    private $bundle;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $pagepartName;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var array
     */
    private $sections = array();

    /**
     * @var bool
     */
    private $behatTest;

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
            ->addOption('prefix', '', InputOption::VALUE_OPTIONAL, 'The prefix to be used in the table name of the generated entity')
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

        $this->createGenerator()->generate($this->bundle, $this->pagepartName, $this->prefix, $this->fields, $this->sections, $this->behatTest);

        $this->assistant->writeSection('PagePart successfully created', 'bg=green;fg=black');
        $this->assistant->writeLine(array(
            'Make sure you update your database first before you test the pagepart:',
            '    Directly update your database:          <comment>app/console doctrine:schema:update --force</comment>',
            '    Create a Doctrine migration and run it: <comment>app/console doctrine:migrations:diff && app/console doctrine:migrations:migrate</comment>',
            ($this->behatTest ? 'A new behat test is created, to run it: <comment>bin/behat --tags \'@'.$this->pagepartName.'\' @'.$this->bundle->getName().'</comment>' : '')
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
        $this->bundle = $this->askForBundleName('pagepart');

        /**
         * Ask the database table prefix
         */
        $this->prefix = $this->askForPrefix(null, $this->bundle->getNamespace());

        /**
         * Ask the name of the pagepart
         */
        $this->assistant->writeLine(array(
            '',
            'The name of your PagePart: For example: <comment>ContentBoxPagePart</comment>',
            '',
        ));
        $generator = $this->getGenerator();
        $bundlePath = $this->bundle->getPath();
        $name = $this->assistant->askAndValidate(
            'PagePart name',
            function ($name) use ($generator, $bundlePath) {
                // Check reserved words
                if ($generator->isReservedKeyword($name)){
                    throw new \InvalidArgumentException(sprintf('"%s" is a reserved word', $name));
                }

                // Name should end on PagePart
                if (!preg_match('/PagePart$/', $name)) {
                    throw new \InvalidArgumentException('The pagepart name must end with PagePart');
                }

                // Name should contain more characters than PagePart
                if (strlen($name) <= strlen('PagePart') || !preg_match('/^[a-zA-Z]+$/', $name)) {
                    throw new \InvalidArgumentException('Invalid pagepart name');
                }

                // Check that entity does not already exist
                if (file_exists($bundlePath.'/Entity/PageParts/'.$name.'.php')) {
                    throw new \InvalidArgumentException(sprintf('PagePart or entity "%s" already exists', $name));
                }

                return $name;
            }
        );
        $this->pagepartName = $name;

        /**
         * Ask which fields need to be present
         */
        $this->assistant->writeLine(array("\nInstead of starting with a blank pagepart, you can add some fields now.\n"));
        $fields = $this->askEntityFields($this->bundle);
        $this->fields = array();
        foreach ($fields as $fieldInfo) {
            if($fieldInfo['type'] == 'image') {
                $this->fields[] = $this->getEntityFields($this->bundle, $this->pagepartName, $this->prefix, $fieldInfo['name'], $fieldInfo['type'],
                    $fieldInfo['extra'], true, $fieldInfo['minHeight'], $fieldInfo['maxHeight'], $fieldInfo['minWidth'], $fieldInfo['maxWidth'], $fieldInfo['mimeTypes']);
            }
            elseif($fieldInfo['type'] == 'media') {
                $this->fields[] = $this->getEntityFields($this->bundle, $this->pagepartName, $this->prefix, $fieldInfo['name'], $fieldInfo['type'],
                    $fieldInfo['extra'], true, null, null, null, null, $fieldInfo['mimeTypes']);
            }
            else $this->fields[] = $this->getEntityFields($this->bundle, $this->pagepartName, $this->prefix, $fieldInfo['name'], $fieldInfo['type'], $fieldInfo['extra'], true);
        }

        /**
         * Ask for which page sections we should enable this pagepart
         */
        $question = 'In which page section configuration file(s) do you want to add the pagepart (multiple possible, separated by comma)';
        $this->sections = $this->askForSections($question, $this->bundle, true);

        /**
         * Ask that you want to create behat tests for the new pagepart, if possible
         */
        if (count($this->sections) > 0 && $this->canGenerateBehatTests($this->bundle)) {
            $this->behatTest = $this->assistant->askConfirmation('Do you want to generate behat tests for this pagepart? (y/n)', 'y');
        } else {
            $this->behatTest = false;
        }
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

        return new PagePartGenerator($filesystem, $registry, '/pagepart', $this->assistant, $this->getContainer());
    }
}
