<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Generator\DefaultEntityGenerator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @deprecated since KunstmaanGeneratorBundle 5.9 and will be removed in KunstmaanGeneratorBundle 6.0.
 *
 * NEXT_MAJOR Remove command, generator and related skeleton templates
 */
class GenerateEntityCommand extends KunstmaanGenerateCommand
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
    private $entityName;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var bool
     */
    private $withRepository;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('kuma:generate:entity')
            ->setDescription('Generates a new Doctrine entity inside a bundle')
            ->addOption('prefix', '', InputOption::VALUE_OPTIONAL, 'The prefix to be used in the table names of the generated entities')
            ->addOption('with-repository', null, InputOption::VALUE_NONE, 'Whether to generate the entity repository or not (y/n)')
            ->setHelp(<<<'EOT'
The <info>kuma:generate:entity</info> task generates a new entity inside a bundle:

<info>php bin/console kuma:generate:entity</info>

The command can also generate the corresponding entity repository class with the
<comment>--with-repository</comment> option:

<info>php bin/console kuma:generate:entity --with-repository</info>

Use the <info>--prefix</info> option to add a prefix to the table names of the generated entities

<info>php bin/console kuma:generate:entity --prefix=demo_</info>
EOT
            );
    }

    /**
     * @return DefaultEntityGenerator
     */
    protected function createGenerator()
    {
        $filesystem = $this->getContainer()->get('filesystem');
        $registry = $this->getContainer()->get('doctrine');

        return new DefaultEntityGenerator($filesystem, $registry, '/entity', $this->assistant, $this->getContainer());
    }

    /**
     * The text to be displayed on top of the generator.
     *
     * @return string|array
     */
    protected function getWelcomeText()
    {
        return 'Welcome to the Kunstmaan entity generator';
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute()
    {
        @trigger_error(sprintf('The "kuma:generate:entity" command is deprecated since KunstmaanGeneratorBundle 5.9 and will be removed in KunstmaanGeneratorBundle 6.0. Use the "make:entity" command of the symfony/maker-bundle instead.'), E_USER_DEPRECATED);

        $this->assistant->writeSection('Entity generation');

        $this->createGenerator()->generate($this->bundle, $this->entityName, $this->prefix, $this->fields, $this->withRepository);

        $this->assistant->writeSection('Entity successfully created', 'bg=green;fg=black');
        $this->assistant->writeLine([
            'Make sure you update your database first before you test the entity:',
            '    Directly update your database:          <comment>bin/console doctrine:schema:update --force</comment>',
            '    Create a Doctrine migration and run it: <comment>bin/console doctrine:migrations:diff && app/console doctrine:migrations:migrate</comment>',
        ]);

        return 0;
    }

    /**
     * Do the interaction with the end user.
     */
    protected function doInteract()
    {
        $this->assistant->writeLine(["This command helps you to generate a new entity.\n"]);

        /*
         * Ask for which bundle we need to create the pagepart
         */
        $this->bundle = $this->askForBundleName('entity');

        /*
         * Ask the database table prefix
         */
        $this->prefix = $this->askForPrefix(null, $this->bundle->getNamespace());

        /*
         * Ask the name of the pagepart
         */
        $this->assistant->writeLine([
            '',
            'The name of your Entity: For example: <comment>Address</comment>',
            '',
        ]);
        $generator = $this->getGenerator();
        $bundlePath = $this->bundle->getPath();
        $name = $this->assistant->askAndValidate(
            'Entity name',
            function ($name) use ($generator, $bundlePath) {
                // Check reserved words
                if ($generator->isReservedKeyword($name)) {
                    throw new \InvalidArgumentException(sprintf('"%s" is a reserved word', $name));
                }

                if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $name)) {
                    throw new \InvalidArgumentException(sprintf('Invalid entity name: %s', $name));
                }

                // Check that entity does not already exist
                if (file_exists($bundlePath . '/Entity/' . $name . '.php')) {
                    throw new \InvalidArgumentException(sprintf('Entity "%s" already exists', $name));
                }

                return $name;
            }
        );
        $this->entityName = $name;

        /*
         * Ask which fields need to be present
         */
        $this->assistant->writeLine(["\nInstead of starting with a blank entity, you can add some fields now.\n"]);
        $fields = $this->askEntityFields($this->bundle);
        $this->fields = array_map(function ($fieldInfo) {
            switch ($fieldInfo['type']) {
                case 'image':
                    return $this->getEntityFields($this->bundle, $this->entityName, $this->prefix, $fieldInfo['name'], $fieldInfo['type'],
                        $fieldInfo['extra'], true, $fieldInfo['minHeight'], $fieldInfo['maxHeight'], $fieldInfo['minWidth'], $fieldInfo['maxWidth'], $fieldInfo['mimeTypes']);

                    break;

                case 'media':
                    return $this->getEntityFields($this->bundle, $this->entityName, $this->prefix, $fieldInfo['name'], $fieldInfo['type'],
                        $fieldInfo['extra'], true, null, null, null, null, $fieldInfo['mimeTypes']);

                    break;

                default:
                    return $this->getEntityFields($this->bundle, $this->entityName, $this->prefix, $fieldInfo['name'], $fieldInfo['type'], $fieldInfo['extra'], true);

                    break;
            }
        }, $fields);

        /*
         * Ask if a repository class needs to be generated
         */
        $this->withRepository = $this->askForWithRepository();
    }

    /**
     * @return bool
     */
    protected function askForWithRepository()
    {
        $withRepositoryOption = $this->assistant->getOption('with-repository');
        if ($withRepositoryOption != 'y' && $withRepositoryOption != 'n') {
            /** @var $question */
            $withRepositoryOption = $this->assistant->askConfirmation("\nDo you want to generate a repository class for the entity ? (y/n)\n", '', '?', false);
        }

        return $withRepositoryOption == 'y';
    }
}
