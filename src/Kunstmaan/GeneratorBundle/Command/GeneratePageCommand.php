<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Generator\PageGenerator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\Finder;

/**
 * Generates a new page
 */
class GeneratePageCommand extends KunstmaanGenerateCommand
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
        $this->setDescription('Generates a new page')
            ->setHelp(<<<EOT
The <info>kuma:generate:page</info> command generates a new page and its configuration.

<info>php app/console kuma:generate:page</info>
EOT
            )
            ->addOption('prefix', '', InputOption::VALUE_OPTIONAL, 'The prefix to be used in the table name of the generated entity')
            ->setName('kuma:generate:page');
    }

    /**
     * {@inheritdoc}
     */
    protected function getWelcomeText()
    {
        return 'Welcome to the Kunstmaan page generator';
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute()
    {
        $this->assistant->writeSection('Page generation');

        $this->createGenerator()->generate($this->bundle, $this->pageName, $this->prefix, $this->fields, $this->template, $this->sections, $this->parentPages);

        $this->assistant->writeSection('Page successfully created', 'bg=green;fg=black');

        if (count($this->parentPages) == 0) {
            $this->assistant->writeLine(array(
                "To use this page you must first add the definition below to the <comment>getPossibleChildTypes</comment> funtion of the parent page:",
                "<comment>    array(</comment>",
                "<comment>        'name' => '".$this->pageName."',</comment>",
                "<comment>        'class'=> '".$this->bundle->getNamespace()."\Entity\Pages\\".$this->pageName."'</comment>",
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

        $this->assistant->writeLine(array("This command helps you to generate a new page.\n"));

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
            'The name of your Page: For example: <comment>SponsorPage</comment>, <comment>NewsOverviewPage</comment>',
            '',
        ));
        $self = $this;
        $generator = $this->getGenerator();
        $bundlePath = $self->bundle->getPath();

        $name = $this->assistant->askAndValidate(
            'Page name',
            function ($name) use ($self, $generator, $bundlePath) {
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
                if (file_exists($bundlePath.'/Entity/Pages/'.$name.'.php')) {
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
        $fields = $this->askEntityFields($this->bundle, array('title', 'pageTitle', 'parent', 'id'));
        $this->fields = array();
        foreach ($fields as $fieldInfo) {
            $this->fields[] = $this->getEntityFields($this->bundle, $this->pageName, $this->prefix, $fieldInfo['name'], $fieldInfo['type'], $fieldInfo['extra'], true);
        }

        /**
         * Ask which default page template we need to use
         */
        $templateSelect = $this->getDefaultTemplateList();
        $this->assistant->writeLine('');
        $templateId = $this->assistant->askSelect('Which page template do you want to use', $templateSelect);
        $templateConfigs = $this->getDefaultTemplateConfigurations();
        $templateConfig = $templateConfigs[$templateId];
        $this->template = $templateConfig['file'];

        /**
         * Ask for which sections pagepart configuration the end user wants to use for the different sections
         */
        $this->assistant->writeLine(array("\nThe select page template consists of these contexts: ".implode(', ', $templateConfig['contexts'])));
        $this->section = array();
        $defaultSectionConfiguration = $this->getDefaultSectionConfigurations();
        foreach ($templateConfig['contexts'] as $context) {
            $question = "Which pagepart configuration would you like to use for the '$context' context";
            $section = $this->askForSections($question, $this->bundle, false, $context, $defaultSectionConfiguration);
            if (is_null($section)) {
                $this->assistant->writeError(sprintf('No section pagepart configuration found for context "%s"', $context), true);
            }
            $this->sections[] = $section;
        }

        /**
         * Ask the parent pages
         */
        $counter = 1;
        $pagesSelect = $parentPages = array();
        $this->parentPages = array();
        // Get the available pages from disc
        $dir = $this->bundle->getPath().'/Entity/Pages/';
        if (file_exists($dir) && is_dir($dir)) {
            $finder = new Finder();
            $finder->files()->in($dir)->depth('== 0');
            foreach ($finder as $file) {
                $temp = $counter++;
                $pagesSelect[$temp] = substr($file->getFileName(), 0, strlen($file->getFileName())-4);
                $parentPages[$temp] = array(
                    'path' => $file->getPathName()
                );
            }
        }
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
     * @return PagePartGenerator
     */
    protected function createGenerator()
    {
        $filesystem = $this->getContainer()->get('filesystem');
        $registry = $this->getContainer()->get('doctrine');

        return new PageGenerator($filesystem, $registry, '/page', $this->assistant);
    }

    /**
     * Get all the available default templates.
     *
     * @return array
     */
    private function getDefaultTemplateList()
    {
        $templates = $this->getDefaultTemplateConfigurations();

        $types = array();
        foreach ($templates as $key => $template) {
            $types[$key] = $template['name'];
        }

        return $types;
    }

    /**
     * Get all the default template configurations.
     *
     * @return array
     */
    private function getDefaultTemplateConfigurations()
    {
        $counter = 1;

        $templates = array();
        $templates[$counter++] = array(
            'name' => 'One column page',
            'contexts' => array('main', 'footer'),
            'file' => 'default-one-column.yml'
        );
        $templates[$counter++] = array(
            'name' => 'Two column page (left sidebar)',
            'contexts' => array('main', 'left_sidebar', 'footer'),
            'file' => 'default-two-column-left.yml'
        );
        $templates[$counter++] = array(
            'name' => 'Two column page (right sidebar)',
            'contexts' => array('main', 'right_sidebar', 'footer'),
            'file' => 'default-two-column-right.yml'
        );
        $templates[$counter++] = array(
            'name' => 'Three column page',
            'contexts' => array('main', 'left_sidebar', 'right_sidebar', 'footer'),
            'file' => 'default-three-column.yml'
        );

        return $templates;
    }

    /**
     * Get all the default section configurations.
     */
    private function getDefaultSectionConfigurations()
    {
        $configs = array();
        $configs['main.yml'] = array(
            'name' => 'Main',
            'context' => 'main',
            'file' => 'main.yml',
        );
        $configs['footer.yml'] = array(
            'name' => 'Footer',
            'context' => 'footer',
            'file' => 'footer.yml',
        );
        $configs['left-sidebar.yml'] = array(
            'name' => 'Left sidebar',
            'context' => 'left_sidebar',
            'file' => 'left-sidebar.yml',
        );
        $configs['right-sidebar.yml'] = array(
            'name' => 'Right sidebar',
            'context' => 'right_sidebar',
            'file' => 'right-sidebar.yml',
        );

        return $configs;
    }
}
