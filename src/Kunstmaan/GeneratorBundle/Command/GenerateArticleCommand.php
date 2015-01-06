<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Generator\ArticleGenerator;
use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Symfony\Component\Console\Input\InputOption;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCommand;

/**
 * Generates classes based on the AbstractArticle classes from KunstmaanArticleBundle
 */
class GenerateArticleCommand extends GenerateDoctrineCommand
{

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
                    new InputOption('dummydata', null, InputOption::VALUE_NONE, 'If set, the task will generate data fixtures to populate your database')
                )
            )
            ->setDescription('Generates Article classes based on KunstmaanArticleBundle')
            ->setHelp(<<<EOT
The <info>kuma:generate:article</info> command generates classes for Articles using the KunstmaanArticleBundle

<info>php app/console kuma:generate:article --namespace=Namespace/NamedBundle --entity=Article</info>

Use the <info>--prefix</info> option to add a prefix to the table names of the generated entities

<info>php app/console kuma:generate:article --namespace=Namespace/NamedBundle --prefix=demo_</info>

Add the <info>--dummydata</info> option to create data fixtures to populate your database

<info>php app/console kuma:generate:article --namespace=Namespace/NamedBundle --dummydata</info>
EOT
            )
            ->setName('kuma:generate:article');
    }

    /**
     * Executes the command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Article Generation');

        GeneratorUtils::ensureOptionsProvided($input, array('namespace', 'entity'));

        $namespace = Validators::validateBundleNamespace($input->getOption('namespace'));
        $bundle = strtr($namespace, array('\\' => ''));
        $entity = ucfirst($input->getOption('entity'));

        $prefix = $input->getOption('prefix');
        $dummydata = $input->getOption('dummydata');

        $bundle = $this
            ->getApplication()
            ->getKernel()
            ->getBundle($bundle);

        $generator = $this->getGenerator($this->getApplication()->getKernel()->getBundle("KunstmaanGeneratorBundle"));
        $generator->generate($bundle, $entity, $prefix, $dummydata, $output);

        $output->writeln(array('Make sure you update your database first before using the created entities:',
                '    Directly update your database:          <comment>app/console doctrine:schema:update --force</comment>',
                '    Create a Doctrine migration and run it: <comment>app/console doctrine:migrations:diff && app/console doctrine:migrations:migrate</comment>')
        );

        if ($dummydata) {
            $output->writeln('    New DataFixtures were created. You can load them via: <comment>app/console doctrine:fixtures:load --fixtures=src/'.str_replace('\\', '/', $bundle->getNamespace()).'/DataFixtures/ORM/ArticleGenerator/ --append</comment>');
        }

        $output->writeln('');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Welcome to the Kunstmaan Article generator');

        $inputAssistant = GeneratorUtils::getInputAssistant($input, $output, $dialog, $this->getApplication()->getKernel(), $this->getContainer());

        $inputAssistant->askForNamespace(array(
            '',
            'This command helps you to generate the Article classes.',
            'You must specify the namespace of the bundle where you want to generate the classes in.',
            'Use <comment>/</comment> instead of <comment>\\ </comment>for the namespace delimiter to avoid any problem.',
            '',
        ));

        // entity
        $entity = $input->getOption('entity') ? $input->getOption('entity') : null;

        if (is_null($entity)) {
            $output->writeln(array(
                '',
                'You must specify a name for the collection of Article entities.',
                'This name will be prefixed before every new entity.',
                'For example entering <comment>News</comment> will result in:',
                '<comment>News</comment>OverviewPage, <comment>News</comment>Page and <comment>News</comment>Author',
                '',
            ));

            $entityValidation = function ($entity) {
                if (empty($entity)) {
                    throw new \RuntimeException('You have to provide a entity name!');
                } else {
                    return $entity;
                }
            };

            $entity = $dialog->askAndValidate($output, $dialog->getQuestion('Name', $entity), $entityValidation, false, $entity);
            $input->setOption('entity', $entity);
        }

        $inputAssistant->askForPrefix();
    }

    protected function createGenerator()
    {
        return new ArticleGenerator($this->getContainer()->get('filesystem'), '/article', $this->getContainer()->getParameter('multilanguage'));
    }
}
