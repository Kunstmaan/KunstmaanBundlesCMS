<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Generator\DefaultPagePartGenerator;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Generates the default form pageparts
 */
class GenerateFormPagePartsCommand extends KunstmaanGenerateCommand
{
    /**
     * @var BundleInterface
     */
    private $bundle;

    /**
     * @var string
     */
    private $prefix;
    /** @var DoctrineHelper */
    private $doctrineHelper;

    public function __construct(DoctrineHelper $doctrineHelper)
    {
        parent::__construct();

        $this->doctrineHelper = $doctrineHelper;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Generates default form pageparts')
            ->setHelp(
                <<<'EOT'
The <info>kuma:generate:form-pageparts</info> command generates the default form pageparts and adds the pageparts configuration.

<info>php bin/console kuma:generate:form-pageparts</info>
EOT
            )
            ->addOption('namespace', '', InputOption::VALUE_OPTIONAL, 'The namespace to generate the default form pageparts in')
            ->addOption('prefix', '', InputOption::VALUE_OPTIONAL, 'The prefix to be used in the table name of the generated entity')
            ->setName('kuma:generate:form-pageparts');
    }

    protected function getWelcomeText()
    {
        return 'Welcome to the Kunstmaan default form pageparts generator';
    }

    protected function doExecute()
    {
        $this->assistant->writeSection('Default Form PageParts generation');

        $pagepartNames = [
            'AbstractFormPagePart',
            'CheckboxPagePart',
            'ChoicePagePart',
            'EmailPagePart',
            'FileUploadPagePart',
            'MultiLineTextPagePart',
            'SingleLineTextPagePart',
            'SubmitButtonPagePart',
        ];

        foreach ($pagepartNames as $pagepartName) {
            $this->createGenerator()->generate($this->bundle, $pagepartName, $this->prefix, [], false);
        }

        $this->assistant->writeSection('PageParts successfully created', 'bg=green;fg=black');
        $this->assistant->writeLine(
            [
                'Make sure you update your database first before you test the pagepart:',
                '    Directly update your database:          <comment>bin/console doctrine:schema:update --force</comment>',
                '    Create a Doctrine migration and run it: <comment>bin/console doctrine:migrations:diff && bin/console doctrine:migrations:migrate</comment>',
            ]
        );

        return 0;
    }

    protected function doInteract()
    {
        if (!$this->isBundleAvailable('KunstmaanPagePartBundle')) {
            $this->assistant->writeError('KunstmaanPagePartBundle not found', true);
        }

        $this->assistant->writeLine(["This command helps you to generate the form pageparts.\n"]);

        /**
         * Ask for which bundle we need to create the pagepart
         */
        $bundleNamespace = $this->assistant->getOptionOrDefault('namespace', null);
        $this->bundle = $this->askForBundleName('pagepart', $bundleNamespace);

        /*
         * Ask the database table prefix
         */
        $this->prefix = $this->askForPrefix(null, $this->bundle->getNamespace());
    }

    /**
     * Get the generator.
     *
     * @return DefaultPagePartGenerator
     */
    protected function createGenerator()
    {
        $filesystem = new Filesystem();
        $registry = $this->getContainer()->get('doctrine');

        return new DefaultPagePartGenerator($filesystem, $registry, '/pagepart', $this->assistant, $this->getContainer(), $this->doctrineHelper);
    }
}
