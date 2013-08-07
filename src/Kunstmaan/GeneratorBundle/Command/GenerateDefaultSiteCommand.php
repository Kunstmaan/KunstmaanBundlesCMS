<?php

namespace Kunstmaan\GeneratorBundle\Command;


use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Kunstmaan\GeneratorBundle\Helper\InputAssistant;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCommand;
use Sensio\Bundle\GeneratorBundle\Generator;

use Kunstmaan\GeneratorBundle\Generator\DefaultSiteGenerator;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\HttpKernel\Kernel;

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
     * @param InputInterface  $input  An InputInterface instance.
     * @param OutputInterface $output An OutputInterface instance.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Site Generation');

        $namespace = Validators::validateBundleNamespace($input->getOption('namespace'));
        $bundle = strtr($namespace, array('\\' => ''));

        $prefix = GeneratorUtils::cleanPrefix($input->getOption('prefix'));
        $bundle = $this
            ->getApplication()
            ->getKernel()
            ->getBundle($bundle);

        $rootDir = $this->getApplication()->getKernel()->getRootDir();

        $generator = $this->getGenerator($this->getApplication()->getKernel()->getBundle("KunstmaanGeneratorBundle"));
        $generator->generate($bundle, $prefix, $rootDir, $output);
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Welcome to the Kunstmaan default site generator');

        /** @var $inputAssistant InputAssistant */
        $inputAssistant = GeneratorUtils::getInputAssistant($input, $output, $dialog, $this->getApplication()->getKernel());

        $inputAssistant->askForNamespace(array(
            '',
            'This command helps you to generate a default site setup.',
            'You must specify the namespace of the bundle where you want to generate the default site setup.',
            'Use <comment>/</comment> instead of <comment>\\ </comment>for the namespace delimiter to avoid any problem.',
            '',
        ));

        $inputAssistant->askForPrefix(array(
            '',
            'You can add a prefix to the table names of the generated entities for example: <comment>projectname_bundlename</comment>',
            "Leave empty if you don't want to specify a tablename prefix.",
            '',
        ));
    }

    protected function createGenerator()
    {
        return new DefaultSiteGenerator($this->getContainer()->get('filesystem'), '/defaultsite');
    }
}
