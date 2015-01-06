<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Generator\SearchPageGenerator;
use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Symfony\Component\Console\Input\InputOption;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCommand;

/**
 * Generates a SearchPage based on the KunstmaanNodeSearchBundle
 */
class GenerateSearchPageCommand extends GenerateDoctrineCommand
{

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputOption('namespace', '', InputOption::VALUE_REQUIRED, 'The namespace to generate the SearchPage in'),
                    new InputOption('prefix', '', InputOption::VALUE_OPTIONAL, 'The prefix to be used in the table names of the generated entities'),
                    new InputOption('createpage', null, InputOption::VALUE_NONE, 'If set, the task will generate data fixtures to populate your database with a search page')
                )
            )
            ->setDescription('Generates a SearchPage based on KunstmaanNodeSearchBundle')
            ->setHelp(<<<EOT
The <info>kuma:generate:searchpage</info> command generates a SearchPage using the KunstmaanNodeSearchBundle and KunstmaanSearchBundle

<info>php app/console kuma:generate:searchpage --namespace=Namespace/NamedBundle</info>

Use the <info>--prefix</info> option to add a prefix to the table names of the generated entities

<info>php app/console kuma:generate:searchpage --namespace=Namespace/NamedBundle --prefix=demo_</info>

Add the <info>--createpage</info> option to create data fixtures to populate your database with a search page

<info>php app/console kuma:generate:article --namespace=Namespace/NamedBundle --createpage</info>
EOT
            )
            ->setName('kuma:generate:searchpage');
    }

    /**
     * Executes the command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Search Page Generation');

        GeneratorUtils::ensureOptionsProvided($input, array('namespace'));

        $namespace = Validators::validateBundleNamespace($input->getOption('namespace'));
        $bundle = strtr($namespace, array('\\' => ''));

        $prefix = $input->getOption('prefix');
        $createPage = $input->getOption('createpage');
        $bundle = $this
            ->getApplication()
            ->getKernel()
            ->getBundle($bundle);

        $rootDir = $this->getApplication()->getKernel()->getRootDir();

        $generator = $this->getGenerator($this->getApplication()->getKernel()->getBundle("KunstmaanGeneratorBundle"));
        $generator->generate($bundle, $prefix, $rootDir, $createPage, $output);

        $output->writeln(array(
                'Make sure you update your database first before you test the pagepart:',
                '    Directly update your database:          <comment>app/console doctrine:schema:update --force</comment>',
                '    Create a Doctrine migration and run it: <comment>app/console doctrine:migrations:diff && app/console doctrine:migrations:migrate</comment>')
        );

        if ($createPage) {
            $output->writeln('    New DataFixtures were created. You can load them via: <comment>app/console doctrine:fixtures:load --fixtures=src/'.str_replace('\\', '/', $bundle->getNamespace()).'/DataFixtures/ORM/SearchPageGenerator/ --append</comment>');
        }

        $output->writeln('');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Welcome to the SearchPage generator');

        $inputAssistant = GeneratorUtils::getInputAssistant($input, $output, $dialog, $this->getApplication()->getKernel(), $this->getContainer());

        $inputAssistant->askForNamespace(array(
            '',
            'This command helps you to generate a SearchPage.',
            'You must specify the namespace of the bundle where you want to generate the SearchPage in.',
            'Use <comment>/</comment> instead of <comment>\\ </comment>for the namespace delimiter to avoid any problem.',
            '',
        ));

        $inputAssistant->askForPrefix();
    }

    protected function createGenerator()
    {
        return new SearchPageGenerator($this->getContainer()->get('filesystem'), '/searchpage');
    }
}
