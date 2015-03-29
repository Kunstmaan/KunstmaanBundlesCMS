<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Generator\DefaultSiteGenerator;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Generates a default website based on Kunstmaan bundles
 */
class GenerateDefaultSiteCommand extends KunstmaanGenerateCommand
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
     * @var bool
     */
    private $demosite;

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setHelp(<<<EOT
The <info>kuma:generate:site</info> command generates an website using the Kunstmaan bundles

<info>php app/console kuma:generate:default-site --namespace=Namespace/NamedBundle</info>

Use the <info>--prefix</info> option to add a prefix to the table names of the generated entities

<info>php app/console kuma:generate:default-site --namespace=Namespace/NamedBundle --prefix=demo_</info>
EOT
            )
            ->setDescription('Generates a basic website based on Kunstmaan bundles with default templates')
            ->addOption('namespace', '', InputOption::VALUE_OPTIONAL, 'The namespace to generate the default website in')
            ->addOption('prefix', '', InputOption::VALUE_OPTIONAL, 'The prefix to be used in the table names of the generated entities')
            ->addOption('demosite', '', InputOption::VALUE_NONE, 'Whether to generate a website with demo contents or a basic website')
            ->setName('kuma:generate:default-site');
    }

    /**
     * {@inheritdoc}
     */
    protected function getWelcomeText()
    {
        return 'Welcome to the Kunstmaan default site generator';
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute()
    {
        $this->assistant->writeSection('Site generation');
        $this->assistant->writeLine(array("This command helps you to generate a default site setup.\n"));

        /**
         * Ask for which bundle we need to create the layout
         */
        $bundleNamespace = $this->assistant->getOptionOrDefault('namespace', null);
        $this->bundle = $this->askForBundleName('layout', $bundleNamespace);

        /**
         * Ask the database table prefix
         */
        $this->prefix = $this->askForPrefix(null, $this->bundle->getNamespace());

        /**
         * If we need to generate a full site, or only the basic structure
         */
        $this->demosite = $this->assistant->getOption('demosite');

        // First we generate the layout if it is not yet generated
	$command = $this->getApplication()->find('kuma:generate:layout');
	$arguments = array(
	    'command'      => 'kuma:generate:layout',
	    '--namespace'  => str_replace('\\', '/', $this->bundle->getNamespace()),
	    '--demosite'   => $this->demosite,
	    '--subcommand' => true
	);
	$input = new ArrayInput($arguments);
	$command->run($input, $this->assistant->getOutput());

	$rootDir = $this->getApplication()->getKernel()->getRootDir().'/../';
	$this->createGenerator()->generate($this->bundle, $this->prefix, $rootDir, $this->demosite);

	// Generate the default pageparts
	$command = $this->getApplication()->find('kuma:generate:default-pageparts');
	$arguments = array(
	    'command'      => 'kuma:generate:default-pageparts',
	    '--namespace'  => str_replace('\\', '/', $this->bundle->getNamespace()),
	    '--prefix'     => $this->prefix,
	    '--contexts'   => 'main',
	    '--quiet'      => true
	);
	$output = new ConsoleOutput(ConsoleOutput::VERBOSITY_QUIET);
	$input = new ArrayInput($arguments);
	$command->run($input, $output);
	$this->assistant->writeLine('Generating default pageparts : <info>OK</info>');

	if ($this->demosite) {
	    // Generate a blog
	    $command = $this->getApplication()->find('kuma:generate:article');
            $arguments = array(
		'command'      => 'kuma:generate:article',
                '--namespace'  => str_replace('\\', '/', $this->bundle->getNamespace()),
		'--prefix'     => $this->prefix,
		'--entity'     => 'Blog',
		'--dummydata'  => true,
		'--quiet'      => true
            );
	    $output = new ConsoleOutput(ConsoleOutput::VERBOSITY_QUIET);
            $input = new ArrayInput($arguments);
	    $command->run($input, $output);
	    $this->assistant->writeLine('Generating blog : <info>OK</info>');
        }

        $this->assistant->writeSection('Site successfully created', 'bg=green;fg=black');
    }

    /**
     * {@inheritdoc}
     */
    protected function doInteract()
    {
    }

    /**
     * Get the generator.
     *
     * @return DefaultSiteGenerator
     */
    protected function createGenerator()
    {
        $filesystem = $this->getContainer()->get('filesystem');
        $registry = $this->getContainer()->get('doctrine');

        return new DefaultSiteGenerator($filesystem, $registry, '/defaultsite', $this->assistant);
    }
}
