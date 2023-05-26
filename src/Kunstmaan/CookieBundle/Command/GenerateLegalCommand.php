<?php

namespace Kunstmaan\CookieBundle\Command;

use Doctrine\Persistence\ManagerRegistry;
use Kunstmaan\CookieBundle\Generator\LegalGenerator;
use Kunstmaan\GeneratorBundle\Command\KunstmaanGenerateCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

final class GenerateLegalCommand extends KunstmaanGenerateCommand
{
    /* @var BundleInterface */
    private $bundle;

    /* @var string */
    private $prefix;

    /** @var string */
    private $demoSite;

    /** @var string */
    private $overrideFiles;

    /** @var Filesystem */
    private $fileSystem;

    /** @var RegistryInterface */
    private $registry;

    public function __construct(Filesystem $fileSystem, ManagerRegistry $registry)
    {
        parent::__construct();

        $this->fileSystem = $fileSystem;
        $this->registry = $registry;
    }

    /**
     * @see Command
     */
    protected function configure()
    {
        $this->setDescription('Generates the legal structure')
            ->setHelp(
                <<<EOT
The <info>kuma:generate:legal</info> command generates a basic legal structure.
This will include some extra pages that will be available.

<info>php bin/console kuma:generate:legal</info>

Use the <info>--namespace</info> option to indicate for which bundle you want to create the legal structure

<info>php bin/console kuma:generate:legal --namespace=Namespace/NamedBundle</info>
EOT
            )
            ->addOption(
                'namespace',
                '',
                InputOption::VALUE_OPTIONAL,
                'The namespace of the bundle where we need to create the legal structure in'
            )
            ->addOption('prefix', '', InputOption::VALUE_OPTIONAL, 'The prefix to be used in the table names of the generated entities')
            ->addOption('demosite', '', InputOption::VALUE_NONE, 'Pass this parameter when the demosite styles/javascipt/pages should be generated')
            ->addOption('overrideFiles', '', InputOption::VALUE_NONE, 'Pass this parameter if you wish to override previously generated files')
            ->setName('kuma:generate:legal');
    }

    protected function getWelcomeText()
    {
        return 'Welcome to the Kunstmaan legal generator';
    }

    protected function doExecute()
    {
        $this->assistant->writeSection('Legal structure generation');

        $rootDir = $this->getApplication()->getKernel()->getProjectDir() . '/';
        $this->createGenerator()->generate($this->bundle, $this->prefix, $rootDir, $this->assistant->getOption('demosite'), $this->assistant->getOption('overrideFiles'));

        $this->assistant->writeSection('Legal structure successfully created', 'bg=green;fg=black');

        return 0;
    }

    protected function doInteract()
    {
        $this->assistant->writeLine(["This command helps you to generate a legal structure for your website.\n"]);

        // Ask for which bundle we need to create the structure
        $bundleNamespace = $this->assistant->getOptionOrDefault('namespace');
        $this->bundle = $this->askForBundleName('legal', $bundleNamespace);

        // Ask the database table prefix
        $this->prefix = $this->askForPrefix(null, $this->bundle->getNamespace());

        // If we need to generate the content or only the pages structure
        $this->demoSite = $this->assistant->getOption('demosite');

        // if you wish to override previously generated files.
        $this->overrideFiles = $this->assistant->getOption('overrideFiles');
    }

    /**
     * Get the generator.
     *
     * @return LegalGenerator
     */
    protected function createGenerator()
    {
        return new LegalGenerator($this->fileSystem, $this->registry, '/legal', $this->assistant, $this->getContainer());
    }
}
