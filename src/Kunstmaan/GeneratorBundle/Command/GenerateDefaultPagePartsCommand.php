<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Generator\DefaultPagePartGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Generates a new pagepart
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
    private $pagepartNames;

    /**
     * @var array
     */
    private $fields;

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
The <info>kuma:generate:defaultpageparts</info> command generates default pageparts and the pageparts configuration.

<info>php app/console kuma:generate:defaultpageparts</info>
EOT
            )
            ->addOption('prefix', '', InputOption::VALUE_OPTIONAL, 'The prefix to be used in the table name of the generated entity')
            ->addOption('delete', '', InputOption::VALUE_OPTIONAL, 'Remove the default pageparts from a bundle')
            ->setName('kuma:generate:pageparts-default');
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

        // ALL THE DEFAULT PAGE PARTS THAT NEED TO BE RENDERED
        // DO NOT FORGET TO ADD THE TEMPLATE FILES THAT GO WITH THE PAGE PART
        $this->pagepartNames = array(
            'AbstractPagePart',
            'LinePagePart',
            'HeaderPagePart',
            'LinkPagePart',
            'RawHtmlPagePart',
            'TextPagePart',
            'TocPagePart',
            'ToTopPagePart',
            'ButtonPagePart'
        );

        foreach($this->pagepartNames as $pagepartName) {
            $this->createGenerator()->generate($this->bundle, $pagepartName, $this->prefix, array(), $this->sections, $this->behatTest);
        }

        $this->assistant->writeSection('PageParts successfully created', 'bg=green;fg=black');
        $this->assistant->writeLine(array(
            'Make sure you update your database first before you test the pagepart:',
            '    Directly update your database:          <comment>app/console doctrine:schema:update --force</comment>',
            '    Create a Doctrine migration and run it: <comment>app/console doctrine:migrations:diff && app/console doctrine:migrations:migrate</comment>',
            ($this->behatTest ? 'A new behat test is created, to run it: <comment>bin/behat --tags \'@'.$this->pagepartName.'\' @'.$this->bundle->getName().'</comment>' : '')
        ));
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
        $this->bundle = $this->askForBundleName('pagepart');

        /**
         * Ask the database table prefix
         */
        $this->prefix = $this->askForPrefix(null, $this->bundle->getNamespace());

        /**
         * Ask for which page sections we should enable this pagepart
         */
        $question = 'In which page section configuration file(s) do you want to add the pageparts (multiple possible, separated by comma)';
        $this->sections = $this->askForSections($question, $this->bundle, true);

        /**
         * Ask that you want to create behat tests for the new pagepart, if possible
         */
        if (count($this->sections) > 0) {
            $behatFile = dirname($this->getContainer()->getParameter('kernel.root_dir').'/') . '/behat.yml';
            $pagePartContext = $this->bundle->getPath() . '/Features/Context/PagePartContext.php';
            $behatTestPage = $this->bundle->getPath() . '/Entity/Pages/BehatTestPage.php';
            // Make sure behat is configured and the PagePartContext and BehatTestPage exits
            if (file_exists($behatFile) && file_exists($pagePartContext) && file_exists($behatTestPage)) {
                $this->behatTest = $this->assistant->askConfirmation('Do you want to generate behat tests for this pagepart? (y/n)', 'y');
            } else {
                $this->behatTest = false;
            }
        }
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
