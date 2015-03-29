<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Generator\DefaultPagePartGenerator;
use Symfony\Component\Console\Input\InputOption;

/**
 * Generates the default pageparts
 */
class GenerateDefaultPagePartsCommand extends KunstmaanGenerateCommand
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
     * @var array
     */
    private $sections;

    /**
     * @var bool
     */
    private $behatTest;

    /**
     * @see Command
     */
    protected function configure()
    {
	$this->setDescription('Generates default pageparts')
	    ->setHelp(<<<EOT
The <info>kuma:generate:default-pageparts</info> command generates the default pageparts and adds the pageparts configuration.

<info>php app/console kuma:generate:default-pageparts</info>
EOT
	    )
	    ->addOption('namespace', '', InputOption::VALUE_OPTIONAL, 'The namespace to generate the default pageparts in')
	    ->addOption('prefix', '', InputOption::VALUE_OPTIONAL, 'The prefix to be used in the table name of the generated entity')
	    ->addOption('contexts', '', InputOption::VALUE_OPTIONAL, 'The contexts were we need to allow the pageparts (separated multiple sections with a comma)')
	    ->setName('kuma:generate:default-pageparts');
    }

    /**
     * {@inheritdoc}
     */
    protected function getWelcomeText()
    {
	return 'Welcome to the Kunstmaan default pageparts generator';
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute()
    {
	$this->assistant->writeSection('Default PageParts generation');

	$pagepartNames = array(
	    'AbstractPagePart',
	    'AudioPagePart',
	    'ButtonPagePart',
	    'DownloadPagePart',
	    'HeaderPagePart',
	    'ImagePagePart',
	    'IntroTextPagePart',
	    'LinePagePart',
	    'LinkPagePart',
	    'RawHtmlPagePart',
	    'TextPagePart',
	    'TocPagePart',
	    'ToTopPagePart',
	    'VideoPagePart',
	);

	foreach ($pagepartNames as $pagepartName) {
	    $this->createGenerator()->generate($this->bundle, $pagepartName, $this->prefix, $this->sections, $this->behatTest);
	}

	$this->assistant->writeSection('PageParts successfully created', 'bg=green;fg=black');
	$this->assistant->writeLine(array(
	    'Make sure you update your database first before you test the pagepart:',
	    '    Directly update your database:          <comment>app/console doctrine:schema:update --force</comment>',
	    '    Create a Doctrine migration and run it: <comment>app/console doctrine:migrations:diff && app/console doctrine:migrations:migrate</comment>')
	);
    }

    /**
     * {@inheritdoc}
     */
    protected function doInteract()
    {
	if (!$this->isBundleAvailable('KunstmaanPagePartBundle')) {
	    $this->assistant->writeError('KunstmaanPagePartBundle not found', true);
	}

	$this->assistant->writeLine(array("This command helps you to generate a new pagepart.\n"));

	/**
	 * Ask for which bundle we need to create the pagepart
	 */
	$bundleNamespace = $this->assistant->getOptionOrDefault('namespace', null);
	$this->bundle = $this->askForBundleName('pagepart', $bundleNamespace);

	/**
	 * Ask the database table prefix
	 */
	$this->prefix = $this->askForPrefix(null, $this->bundle->getNamespace());

	/**
	 * Ask for which page sections we should enable this pagepart
	 */
	$contexts = $this->assistant->getOptionOrDefault('contexts', null);
	if ($contexts) {
	    $contexts = explode(',', $contexts);
	    array_walk($contexts, 'trim');

	    $this->sections = array();
	    $allSections = $this->getAvailableSections($this->bundle);
	    foreach ($allSections as $section) {
		if (in_array($section['context'], $contexts)) {
		    $this->sections[] = $section['file'];
		}
	    }
	} else {
	    $question = 'In which page section configuration file(s) do you want to add the pageparts (multiple possible, separated by comma)';
	    $this->sections = $this->askForSections($question, $this->bundle, true);
	}

	/**
	 * Check that we can create behat tests for the default pagepart
	 */
	$this->behatTest = (count($this->sections) > 0 && $this->canGenerateBehatTests($this->bundle));
    }

    /**
     * Get the generator.
     *
     * @return DefaultPagePartGenerator
     */
    protected function createGenerator()
    {
	$filesystem = $this->getContainer()->get('filesystem');
	$registry = $this->getContainer()->get('doctrine');

	return new DefaultPagePartGenerator($filesystem, $registry, '/pagepart', $this->assistant, $this->getContainer());
    }
}
