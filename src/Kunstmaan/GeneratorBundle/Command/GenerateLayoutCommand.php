<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Generator\LayoutGenerator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Generates de default layout
 */
class GenerateLayoutCommand extends KunstmaanGenerateCommand
{
    /**
     * @var BundleInterface
     */
    private $bundle;

    private $browserSyncUrl;

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Generates a basic layout')
            ->setHelp(<<<'EOT'
The <info>kuma:generate:layout</info> command generates a basic website layout.

<info>php bin/console kuma:generate:layout</info>

Use the <info>--namespace</info> option to indicate for which bundle you want to create the layout

<info>php bin/console kuma:generate:layout --namespace=Namespace/NamedBundle</info>
EOT
            )
            ->addOption('namespace', '', InputOption::VALUE_OPTIONAL, 'The namespace of the bundle where we need to create the layout in')
            ->addOption('subcommand', '', InputOption::VALUE_OPTIONAL, 'Whether the command is called from an other command or not')
            ->addOption('demosite', '', InputOption::VALUE_NONE, 'Pass this parameter when the demosite styles/javascipt should be generated')
            ->addOption('groundcontrol', '', InputOption::VALUE_NONE, 'Pass this parameter to use Groundcontrol in favor of Webpack Encore')
            ->addOption('browsersync', '', InputOption::VALUE_OPTIONAL, 'The URI that will be used for browsersync to connect')
            ->setName('kuma:generate:layout');
    }

    protected function getWelcomeText()
    {
        if (!$this->isSubCommand()) {
            return 'Welcome to the Kunstmaan layout generator';
        } else {
            return null;
        }
    }

    protected function doExecute()
    {
        if (!$this->isSubCommand()) {
            $this->assistant->writeSection('Layout generation');
        }

        $rootDir = $this->getApplication()->getKernel()->getProjectDir() . '/';
        $this->createGenerator()->generate($this->bundle, $rootDir, $this->assistant->getOption('demosite'), $this->browserSyncUrl, $this->assistant->getOption('groundcontrol'));

        if (!$this->isSubCommand()) {
            $this->assistant->writeSection('Layout successfully created', 'bg=green;fg=black');
        }

        return 0;
    }

    protected function doInteract()
    {
        if (!$this->isSubCommand()) {
            $this->assistant->writeLine(["This command helps you to generate a basic layout for your website.\n"]);
        }

        /**
         * Ask for which bundle we need to create the layout
         */
        $bundleNamespace = $this->assistant->getOptionOrDefault('namespace', null);
        $this->bundle = $this->askForBundleName('layout', $bundleNamespace);
        $this->browserSyncUrl = $this->assistant->getOptionOrDefault('browsersync', null);

        if (null === $this->browserSyncUrl && $this->assistant->getOption('groundcontrol', null)) {
            $this->browserSyncUrl = $this->assistant->ask('Which URL would you like to configure for browserSync?', 'https://myproject.dev');
        }
    }

    /**
     * Get the generator.
     *
     * @return LayoutGenerator
     */
    protected function createGenerator()
    {
        $filesystem = new Filesystem();
        $registry = $this->getContainer()->get('doctrine');

        return new LayoutGenerator($filesystem, $registry, '/layout', $this->assistant, $this->getContainer());
    }

    /**
     * Check that the command is ran as sub command or not.
     */
    private function isSubCommand(): bool
    {
        return $this->assistant->getOptionOrDefault('subcommand', false);
    }
}
