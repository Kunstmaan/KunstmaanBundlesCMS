<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Generator\ArticleGenerator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Generates classes based on the AbstractArticle classes from KunstmaanArticleBundle
 */
class GenerateArticleCommand extends KunstmaanGenerateCommand
{
    /**
     * @var BundleInterface
     */
    private $bundle;

    /**
     * @var string
     */
    private $entity;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $bundleWithHomePage;

    /**
     * @var array
     */
    private $parentPages = array();

    /**
     * @var bool
     */
    private $usesAuthor;

    /**
     * @var bool
     */
    private $usesCategories;

    /**
     * @var bool
     */
    private $usesTags;

    /**
     * @var bool
     */
    private $dummydata;

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputOption('namespace', '', InputOption::VALUE_REQUIRED, 'The namespace to generate the Article classes in'),
                    new InputOption('entity', '', InputOption::VALUE_REQUIRED, 'The article class name ("News", "Press", ..."'),
                    new InputOption('prefix', '', InputOption::VALUE_OPTIONAL, 'The prefix to be used in the table names of the generated entities'),
                    new InputOption('namespacehomepage', '', InputOption::VALUE_OPTIONAL, 'The namespace of the home page entity'),
                    new InputOption('articleoverviewpageparent', '', InputOption::VALUE_OPTIONAL, 'Shortnames of the pages that can have the article overview page as a child (comma separated)'),
                    new InputOption('with-author', null, InputOption::VALUE_NONE, 'If set, you can use authors'),
                    new InputOption('with-category', null, InputOption::VALUE_NONE, 'If set, you can use categories'),
                    new InputOption('with-tag', null, InputOption::VALUE_NONE, 'If set, the you can use tags'),
                    new InputOption('dummydata', null, InputOption::VALUE_NONE, 'If set, the task will generate data fixtures to populate your database'),
                )
            )
            ->setDescription('Generates Article classes based on KunstmaanArticleBundle')
            ->setHelp(<<<'EOT'
The <info>kuma:generate:article</info> command generates classes for Articles using the KunstmaanArticleBundle

<info>php bin/console kuma:generate:article --namespace=Namespace/NamedBundle --entity=Article</info>

Use the <info>--prefix</info> option to add a prefix to the table names of the generated entities

<info>php bin/console kuma:generate:article --namespace=Namespace/NamedBundle --prefix=demo_</info>

Add the <info>--dummydata</info> option to create data fixtures to populate your database

<info>php bin/console kuma:generate:article --namespace=Namespace/NamedBundle --dummydata</info>
EOT
            )
            ->setName('kuma:generate:article');
    }

    /**
     * @return ArticleGenerator
     */
    protected function createGenerator()
    {
        $filesystem = $this->getContainer()->get('filesystem');
        $registry = $this->getContainer()->get('doctrine');

        return new ArticleGenerator($filesystem, $registry, '/article', $this->parentPages, $this->assistant, $this->getContainer());
    }

    /**
     * Do the interaction with the end user.
     */
    protected function doInteract()
    {
        /**
         * Ask for which bundle we need to create the layout
         */
        $bundleNamespace = $this->assistant->getOptionOrDefault('namespace', null);
        $this->bundle = $this->askForBundleName('article classes', $bundleNamespace);

        /*
         * Entity
         */
        $this->entity = $this->assistant->getOptionOrDefault('entity', null);

        if (is_null($this->entity)) {
            $this->assistant->writeLine(array(
                'You must specify a name for the collection of Article entities.',
                'This name will be prefixed before every new entity.',
                'For example entering <comment>News</comment> will result in:',
                '<comment>News</comment>OverviewPage, <comment>News</comment>Page and <comment>News</comment>Author',
            ));

            $generator = $this->getGenerator();
            $this->entity = $this->assistant->askAndValidate(
                'Article class name',
                function ($name) use ($generator) {
                    // Check reserved words
                    if ($generator->isReservedKeyword($name)) {
                        throw new \InvalidArgumentException(sprintf('"%s" is a reserved word', $name));
                    }
                    if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $name)) {
                        throw new \InvalidArgumentException(sprintf('Invalid entity name: %s', $name));
                    }

                    return $name;
                }
            );
        }

        /*
         * Ask if author will be used
         */
        $this->usesAuthor = $this->askForAuthor();

        /*
         * Ask if category will be used
         */
        $this->usesCategories = $this->askForCategories();

        /*
         * Ask if tag will be used
         */
        $this->usesTags = $this->askForTags();

        /**
         * Ask the parent pages
         */
        $bundleWithHomePageNamespace = $this->assistant->getOptionOrDefault('namespacehomepage', null);
        $this->bundleWithHomePage = $this->askForBundleName('', $bundleWithHomePageNamespace, 'In which bundle is the page that you will use as parent for the overview page ?', "%sThe bundle %s will be used for the parent of the overview page.\n");
        $parentPages = $this->getAvailablePages($this->bundleWithHomePage);
        $pagesSelect = array_map(function ($item) {
            return $item['name'];
        }, $parentPages);
        if (count($pagesSelect) > 0) {
            $this->assistant->writeLine('');
            $parentPageNames = $this->assistant->getOptionOrDefault('articleoverviewpageparent', null);
            if (null !== $parentPageNames) {
                $parentPageNames = explode(',', $parentPageNames);
                foreach ($parentPageNames as $parentPageName) {
                    $id = array_search($parentPageName, $pagesSelect);
                    if (false !== $id) {
                        $this->parentPages[] = $parentPages[$id]['path'];
                    }
                }
            }

            if (empty($this->parentPages)) {
                $parentPageIds = $this->assistant->askSelect('Which existing page(s) can have the new overview page as sub-page (multiple possible, separated by comma)', $pagesSelect, null, true);
                foreach ($parentPageIds as $id) {
                    $this->parentPages[] = $parentPages[$id]['path'];
                }
            }
        }

        /*
         * Ask the database table prefix
         */
        $this->prefix = $this->assistant->getOptionOrDefault('prefix', null);
        if (is_null($this->prefix)) {
            $this->prefix = $this->askForPrefix(null, $this->bundle->getNamespace());
        }

        /*
         * Ask for data fixtures
         */
        $this->dummydata = $this->askForDummydata();
    }

    /**
     * This function implements the final execution of the Generator.
     * It calls the execute function with the correct parameters.
     */
    protected function doExecute()
    {
        $this->assistant->writeSection('Article classes generation');

        $this->createGenerator()->generate($this->bundle, $this->entity, $this->prefix, $this->getContainer()->getParameter('multilanguage'), $this->usesAuthor, $this->usesCategories, $this->usesTags, $this->bundleWithHomePage, $this->dummydata);

        $this->assistant->writeSection('Article classes successfully created', 'bg=green;fg=black');
        $this->assistant->writeLine(array(
            'Make sure you update your database first before you test the pagepart:',
            '    Directly update your database:          <comment>bin/console doctrine:schema:update --force</comment>',
            '    Create a Doctrine migration and run it: <comment>bin/console doctrine:migrations:diff && bin/console doctrine:migrations:migrate</comment>',
        ));
    }

    /**
     * The text to be displayed on top of the generator.
     *
     * @return string|array
     */
    protected function getWelcomeText()
    {
        return 'Welcome to the Kunstmaan article generator';
    }

    /**
     * @return bool
     */
    protected function askForDummydata()
    {
        $dummydataOption = $this->assistant->getOption('dummydata');
        if ($dummydataOption != 'y' && $dummydataOption != 'n') {
            /** @var $question */
            $dummydataOption = $this->assistant->askConfirmation("\nDo you want to generate data fixtures to populate your database ? (y/n)\n", 'n', '?', false);
        }

        return $dummydataOption == 'y';
    }

    /**
     * @return bool
     */
    protected function askForCategories()
    {
        $categoryOption = $this->assistant->getOption('with-category');
        if ($categoryOption != 'y' && $categoryOption != 'n') {
            /** @var $question */
            $categoryOption = $this->assistant->askConfirmation("\nDo you want to use categories ? (y/n)\n", 'y', '?', true);
        }

        return $categoryOption == 'y';
    }

    /**
     * @return bool
     */
    protected function askForTags()
    {
        $tagOption = $this->assistant->getOption('with-tag');
        if ($tagOption != 'y' && $tagOption != 'n') {
            /** @var $question */
            $tagOption = $this->assistant->askConfirmation("\nDo you want to use tags ? (y/n)\n", 'y', '?', true);
        }

        return $tagOption == 'y';
    }

    /**
     * @return bool
     */
    protected function askForAuthor()
    {
        $authorOption = $this->assistant->getOption('with-author');
        if ($authorOption != 'y' && $authorOption != 'n') {
            /** @var $question */
            $authorOption = $this->assistant->askConfirmation("\nDo you want to authors ? (y/n)\n", 'y', '?', true);
        }

        return $authorOption == 'y';
    }
}
