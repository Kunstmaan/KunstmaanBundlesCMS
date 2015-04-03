<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Generator\LayoutGenerator;
use Symfony\Component\Console\Input\InputOption;

/**
 * Generates de default layout
 */
class GenerateLayoutCommand extends KunstmaanGenerateCommand
{
    /**
     * @var BundleInterface
     */
    private $bundle;

    /**
     * @see Command
     */
    protected function configure()
    {
        $this->setDescription('Generates a basic layout')
            ->setHelp(<<<EOT
The <info>kuma:generate:layout</info> command generates a basic website layout.

<info>php app/console kuma:generate:layout</info>

Use the <info>--namespace</info> option to indicate for which bundle you want to create the layout

<info>php app/console kuma:generate:layout --namespace=Namespace/NamedBundle</info>
EOT
            )
            ->addOption('namespace', '', InputOption::VALUE_OPTIONAL, 'The namespace of the bundle where we need to create the layout in')
            ->addOption('subcommand', '', InputOption::VALUE_OPTIONAL, 'Whether the command is called from an other command or not')
	    ->addOption('demosite', '', InputOption::VALUE_NONE, 'Pass this parameter when the demosite styles/javascipt should be generated')
            ->setName('kuma:generate:layout');
    }

    /**
     * {@inheritdoc}
     */
    protected function getWelcomeText()
    {
        if (!$this->isSubCommand()) {
            return 'Welcome to the Kunstmaan layout generator';
        } else {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute()
    {
        if (!$this->isSubCommand()) {
            $this->assistant->writeSection('Layout generation');
        }

        $rootDir = $this->getApplication()->getKernel()->getRootDir().'/../';
	$this->createGenerator()->generate($this->bundle, $rootDir, $this->assistant->getOption('demosite'));

        if (!$this->isSubCommand()) {
            $this->assistant->writeSection('Layout successfully created', 'bg=green;fg=black');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doInteract()
    {
        if (!$this->isSubCommand()) {
            $this->assistant->writeLine(array("This command helps you to generate a basic layout for your website.\n"));
        }

        /**
         * Ask for which bundle we need to create the layout
         */
        $bundleNamespace = $this->assistant->getOptionOrDefault('namespace', null);
        $this->bundle = $this->askForBundleName('layout', $bundleNamespace);
    }

    /**
     * Get the generator.
     *
     * @return LayoutGenerator
     */
    protected function createGenerator()
    {
        $filesystem = $this->getContainer()->get('filesystem');
        $registry = $this->getContainer()->get('doctrine');

        return new LayoutGenerator($filesystem, $registry, '/layout', $this->assistant);
    }

    /**
     * Check that the command is ran as sub command or not.
     *
     * @return bool
     */
    private function isSubCommand()
    {
        return $this->assistant->getOptionOrDefault('subcommand', false);
    }
}
