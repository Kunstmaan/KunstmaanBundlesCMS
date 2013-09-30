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
EOT
            )
            ->addOption('namespace', '', InputOption::VALUE_OPTIONAL, 'The namespace of the bundle where we need to create the layout in')
            ->setName('kuma:generate:layout');
    }

    /**
     * {@inheritdoc}
     */
    protected function getWelcomeText()
    {
        return 'Welcome to the Kunstmaan layout generator';
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute()
    {
        $this->assistant->writeSection('Layout generation');

        $rootDir = $this->getApplication()->getKernel()->getRootDir().'/../';
        $this->createGenerator()->generate($this->bundle, $rootDir);

        $this->assistant->writeSection('Layout successfully created', 'bg=green;fg=black');
    }

    /**
     * {@inheritdoc}
     */
    protected function doInteract()
    {
        $this->assistant->writeLine(array("This command helps you to generate a basic layout for your website.\n"));

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
}
