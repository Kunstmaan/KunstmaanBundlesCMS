<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Generator\SearchPageGenerator;
use Symfony\Component\Console\Input\InputOption;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCommand;
use Sensio\Bundle\GeneratorBundle\Generator;

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
                     new InputOption('prefix', '', InputOption::VALUE_OPTIONAL, 'The prefix to be used in the table names of the generated entities')
                )
            )
            ->setDescription('Generates a SearchPage based on KunstmaanNodeSearchBundle')
            ->setHelp(<<<EOT
The <info>kuma:generate:searchpage</info> command generates a SearchPage using the KunstmaanNodeSearchBundle and KunstmaanSearchBundle

<info>php app/console kuma:generate:searchpage --namespace=Namespace/NamedBundle</info>

Use the <info>--prefix</info> option to add a prefix to the table names of the generated entities

<info>php app/console kuma:generate:searchpage --namespace=Namespace/NamedBundle --prefix=demo_</info>
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

        foreach (array('namespace') as $option) {
            if (null === $input->getOption($option)) {
                throw new \RuntimeException(sprintf('The "%s" option must be provided.', $option));
            }
        }

        $namespace = Validators::validateBundleNamespace($input->getOption('namespace'));
        $bundle = strtr($namespace, array('\\' => ''));

        $prefix = $input->getOption('prefix');
        $bundle = $this
            ->getApplication()
            ->getKernel()
            ->getBundle($bundle);
        $dialog->writeSection($output, 'Search Page Generation');
        $rootDir = $this->getApplication()->getKernel()->getRootDir();

        $generator = $this->getGenerator($this->getApplication()->getKernel()->getBundle("KunstmaanGeneratorBundle"));
        $generator->generate($bundle, $prefix, $rootDir, $output);
        $output->writeln('Make sure you update your database first before using the created entities:');
        $output->writeln('    Directly update your database:          <comment>app/console doctrine:schema:update --force</comment>');
        $output->writeln('    Create a Doctrine migration and run it: <comment>app/console doctrine:migrations:diff && app/console doctrine:migrations:migrate</comment>');
        $output->writeln('');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Welcome to the Kunstmaan default site generator');

        // namespace
        $namespace = null;
        try {
            $namespace = $input->getOption('namespace') ? Validators::validateBundleNamespace($input->getOption('namespace')) : null;
        } catch (\Exception $error) {
            $output->writeln($dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }

        if (is_null($namespace)) {
            $output->writeln(array(
                '',
                'This command helps you to generate a SearchPage.',
                'You must specify the namespace of the bundle where you want to generate the SearchPage in.',
                'Use <comment>/</comment> instead of <comment>\\ </comment>for the namespace delimiter to avoid any problem.',
                '',
            ));

            $namespace = $dialog->askAndValidate($output, $dialog->getQuestion('Bundle namespace', $namespace), array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateBundleNamespace'), false, $namespace);
            $input->setOption('namespace', $namespace);
        }

        // prefix
        $prefix = $input->getOption('prefix') ? $input->getOption('prefix') : null;

        if (is_null($prefix)) {
            $output->writeln(array(
                '',
                'You can add a prefix to the table names of the generated entities for example: <comment>demo_</comment>',
                "Leave empty if you don't want to specify a tablename prefix.",
                '',
            ));

            $prefix = $dialog->ask($output, $dialog->getQuestion('Tablename prefix', $prefix), $prefix);
            $input->setOption('prefix', empty($prefix) ? null : $prefix);
        }
    }

    protected function createGenerator()
    {
        return new SearchPageGenerator($this->getContainer()->get('filesystem'), '/searchpage');
    }
}
