<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCommand;
use Sensio\Bundle\GeneratorBundle\Generator;

use Kunstmaan\GeneratorBundle\Generator\DefaultSiteGenerator;

/**
 * Generates a default website based on Kunstmaan bundles
 */
class GenerateDefaultSiteCommand extends GenerateDoctrineCommand
{

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                     new InputOption('namespace', '', InputOption::VALUE_REQUIRED, 'The namespace to generate the default website in'),
                     new InputOption('prefix', '', InputOption::VALUE_OPTIONAL, 'The prefix to be used in the table names of the generated entities')
                )
            )
            ->setDescription('Generates a basic website based on Kunstmaan bundles with default templates')
            ->setHelp(<<<EOT
The <info>kuma:generate:site</info> command generates an website using the Kunstmaan bundles

<info>php app/console kuma:generate:default-site --namespace=Namespace/NamedBundle</info>

Use the <info>--prefix</info> option to add a prefix to the table names of the generated entities

<info>php app/console kuma:generate:default-site --namespace=Namespace/NamedBundle --prefix=demo_</info>
EOT
            )
            ->setName('kuma:generate:default-site');
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
        $dialog->writeSection($output, 'Site Generation');
        $rootDir = $this->getApplication()->getKernel()->getRootDir();

        $generator = $this->getGenerator($this->getApplication()->getKernel()->getBundle("KunstmaanGeneratorBundle"));
        $generator->generate($bundle, $prefix, $rootDir, $output);
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
                'This command helps you to generate a default site setup.',
                'You must specify the namespace of the bundle where you want to generate the default site setup.',
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
        return new DefaultSiteGenerator($this->getContainer()->get('filesystem'), '/defaultsite');
    }
}
