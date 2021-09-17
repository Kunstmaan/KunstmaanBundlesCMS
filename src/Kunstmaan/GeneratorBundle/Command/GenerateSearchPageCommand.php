<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Generator\SearchPageGenerator;
use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Kunstmaan\GeneratorBundle\Helper\Sf4AppBundle;
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCommand;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Kernel;

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
                [
                    new InputOption('namespace', '', InputOption::VALUE_REQUIRED, 'The namespace to generate the SearchPage in. This option is deprecated when using this bundle with symfony 4.'),
                    new InputOption('prefix', '', InputOption::VALUE_OPTIONAL, 'The prefix to be used in the table names of the generated entities'),
                    new InputOption('createpage', null, InputOption::VALUE_NONE, 'If set, the task will generate data fixtures to populate your database with a search page'),
                ]
            )
            ->setDescription('Generates a SearchPage based on KunstmaanNodeSearchBundle')
            ->setHelp(<<<'EOT'
The <info>kuma:generate:searchpage</info> command generates a SearchPage using the KunstmaanNodeSearchBundle and KunstmaanSearchBundle

<info>php bin/console kuma:generate:searchpage --namespace=Namespace/NamedBundle</info>

Use the <info>--prefix</info> option to add a prefix to the table names of the generated entities

<info>php bin/console kuma:generate:searchpage --namespace=Namespace/NamedBundle --prefix=demo_</info>

Add the <info>--createpage</info> option to create data fixtures to populate your database with a search page

<info>php bin/console kuma:generate:article --namespace=Namespace/NamedBundle --createpage</info>
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
        $questionHelper = $this->getQuestionHelper();
        $questionHelper->writeSection($output, 'Search Page Generation');

        $prefix = $input->getOption('prefix');
        $createPage = $input->getOption('createpage');
        if (Kernel::VERSION_ID < 40000) {
            GeneratorUtils::ensureOptionsProvided($input, ['namespace']);

            $namespace = Validators::validateBundleNamespace($input->getOption('namespace'));
            $bundle = strtr($namespace, ['\\' => '']);

            $bundle = $this
                ->getApplication()
                ->getKernel()
                ->getBundle($bundle);
        } else {
            $bundle = new Sf4AppBundle($this->getContainer()->getParameter('kernel.project_dir'));
        }

        $rootDir = $this->getApplication()->getKernel()->getProjectDir();

        $generator = $this->getGenerator($this->getApplication()->getKernel()->getBundle('KunstmaanGeneratorBundle'));
        $generator->generate($bundle, $prefix, $rootDir, $createPage, $output);

        $output->writeln([
                'Make sure you update your database first before you test the pagepart:',
                '    Directly update your database:          <comment>bin/console doctrine:schema:update --force</comment>',
                '    Create a Doctrine migration and run it: <comment>bin/console doctrine:migrations:diff && bin/console doctrine:migrations:migrate</comment>', ]
        );

        if ($createPage) {
            $output->writeln('    New DataFixtures were created. You can load them via: <comment>bin/console doctrine:fixtures:load --fixtures=src/' . str_replace('\\', '/', $bundle->getNamespace()) . '/DataFixtures/ORM/SearchPageGenerator/ --append</comment>');
        }

        $output->writeln('');

        return 0;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();
        $questionHelper->writeSection($output, 'Welcome to the SearchPage generator');

        $inputAssistant = GeneratorUtils::getInputAssistant($input, $output, $questionHelper, $this->getApplication()->getKernel(), $this->getContainer());

        if (Kernel::VERSION_ID >= 40000) {
            $inputAssistant->askForPrefix();

            return;
        }

        $inputAssistant->askForNamespace([
            '',
            'This command helps you to generate a SearchPage.',
            'You must specify the namespace of the bundle where you want to generate the SearchPage in.',
            'Use <comment>/</comment> instead of <comment>\\ </comment>for the namespace delimiter to avoid any problem.',
            '',
        ]);

        $inputAssistant->askForPrefix();
    }

    protected function createGenerator()
    {
        return new SearchPageGenerator($this->getContainer()->get('filesystem'), '/searchpage', $this->getContainer()->getParameter('kernel.project_dir'));
    }
}
