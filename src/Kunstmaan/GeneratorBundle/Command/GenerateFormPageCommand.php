<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Generator\FormPageGenerator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Generates a new formPage
 */
class GenerateFormPageCommand extends KunstmaanGenerateCommand
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
     * @var string
     */
    private $pageName;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var array
     */
    private $template;

    /**
     * @var array
     */
    private $sections;

    /**
     * @var array
     */
    private $parentPages;

    /**
     * @see Command
     */
    protected function configure()
    {
        $this->setDescription('Generates a new formpage')
            ->setHelp(<<<EOT
The <info>kuma:generate:formpage</info> command generates a new formpage and its configuration.

<info>php app/console kuma:generate:formpage</info>
EOT
            )
            ->addOption('prefix', '', InputOption::VALUE_OPTIONAL, 'The prefix to be used in the table name of the generated entity')
            ->setName('kuma:generate:formpage');
    }

    /**
     * {@inheritdoc}
     */
    protected function getWelcomeText()
    {
        return 'Welcome to the Kunstmaan formpage generator';
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute()
    {
        $this->assistant->writeSection('FormPage generation');
        $this->template = strtolower($this->pageName);
        $this->sections = array(strtolower($this->pageName));
        $this->createGenerator()->generate($this->bundle, $this->pageName, $this->prefix, $this->fields, $this->template, $this->sections, $this->parentPages);

        $this->assistant->writeSection('FormPage successfully created', 'bg=green;fg=black');

        if (count($this->parentPages) == 0) {
            $this->assistant->writeLine(array(
                "To use this page you must first add the definition below to the <comment>getPossibleChildTypes</comment> funtion of the parent page:",
                "<comment>    array(</comment>",
                "<comment>        'name' => '".$this->pageName."',</comment>",
                "<comment>        'class'=> '".$this->bundle->getNamespace()."\\Entity\\Pages\\".$this->pageName."'</comment>",
                "<comment>    ),</comment>",
                ""
            ));
        }

        $this->assistant->writeLine(array(
            'Make sure you update your database first before you use the page:',
            '    Directly update your database:          <comment>app/console doctrine:schema:update --force</comment>',
            '    Create a Doctrine migration and run it: <comment>app/console doctrine:migrations:diff && app/console doctrine:migrations:migrate</comment>',
            ''
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

        $this->assistant->writeLine(array("This command helps you to generate a new formpage.\n"));

        /**
         * Ask for which bundle we need to create the pagepart
         */
        $this->bundle = $this->askForBundleName('page');

        /**
         * Ask the database table prefix
         */
        $this->prefix = $this->askForPrefix(null, $this->bundle->getNamespace());

        /**
         * Ask the name of the pagepart
         */
        $this->assistant->writeLine(array(
            '',
            'The name of your FormPage: For example: <comment>ContactPage</comment>, <comment>OrderPage</comment>',
            '',
        ));
        $generator = $this->getGenerator();
        $bundlePath = $this->bundle->getPath();

        $name = $this->assistant->askAndValidate(
            'FormPage name',
            function ($name) use ($generator, $bundlePath) {
                // Check reserved words
                if ($generator->isReservedKeyword($name)){
                    throw new \InvalidArgumentException(sprintf('"%s" is a reserved word', $name));
                }

                // Name should end on Page
                if (!preg_match('/Page$/', $name)) {
                    throw new \InvalidArgumentException('The page name must end with Page');
                }

                // Name should contain more characters than Page
                if (strlen($name) <= strlen('Page') || !preg_match('/^[a-zA-Z]+$/', $name)) {
                    throw new \InvalidArgumentException('Invalid page name');
                }

                // Check that entity does not already exist
                if (file_exists($bundlePath . '/Entity/Pages/' . $name . '.php')) {
                    throw new \InvalidArgumentException(sprintf('Page or entity "%s" already exists', $name));
                }

                return $name;
            }
        );
        $this->pageName = $name;

        /**
         * Ask which fields need to be present
         */
        $this->assistant->writeLine(array("\nInstead of starting with a blank page, you can add some fields now.\n"));
        $fields = $this->askEntityFields($this->bundle, array('title', 'pageTitle', 'parent', 'id', 'thanks', 'subject', 'fromEmail', 'toEmail'));
        $this->fields = array();
        foreach ($fields as $fieldInfo) {
            $this->fields[] = $this->getEntityFields($this->bundle, $this->pageName, $this->prefix, $fieldInfo['name'], $fieldInfo['type'], $fieldInfo['extra'], true);
        }

        /**
         * Ask the parent pages
         */
        $parentPages = $this->getAvailablePages($this->bundle);
        $pagesSelect = array_map(function ($item) { return $item['name']; }, $parentPages);
        if (count($pagesSelect) > 0) {
            $this->assistant->writeLine('');
            $parentPageIds = $this->assistant->askSelect('Which existing page(s) can have the new page as sub-page (multiple possible, separated by comma)', $pagesSelect, null, true);
            foreach ($parentPageIds as $id) {
                $this->parentPages[] = $parentPages[$id]['path'];
            }
        }
    }

    /**
     * Get the generator.
     *
     * @return FormPageGenerator
     */
    protected function createGenerator()
    {
        $filesystem = $this->getContainer()->get('filesystem');
        $registry = $this->getContainer()->get('doctrine');

        return new FormPageGenerator($filesystem, $registry, '/page', $this->assistant);
    }
}
