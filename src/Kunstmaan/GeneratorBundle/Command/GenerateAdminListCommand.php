<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Kunstmaan\GeneratorBundle\Generator\AdminListGenerator;
use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;

/**
 * Generates a KunstmaanAdminList
 */
class GenerateAdminListCommand extends GenerateDoctrineCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputOption(
                        'entity',
                        '',
                        InputOption::VALUE_REQUIRED,
                        'The entity class name to create an admin list for (shortcut notation)'
                    ),
                )
            )
            ->setDescription('Generates a KunstmaanAdminList')
            ->setHelp(
                <<<EOT
                The <info>kuma:generate:adminlist</info> command generates an AdminList for a Doctrine ORM entity.

<info>php app/console kuma:generate:adminlist Bundle:Entity</info>
EOT
            )
            ->setName('kuma:generate:adminlist');
    }

    /**
     * Executes the command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @throws \RuntimeException
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();

        GeneratorUtils::ensureOptionsProvided($input, array('entity'));

        $entity = Validators::validateEntityName($input->getOption('entity'));
        list($bundle, $entity) = $this->parseShortcutNotation($entity);

        $entityClass = $this->getContainer()->get('doctrine')->getAliasNamespace($bundle) . '\\' . $entity;
        $metadata    = $this->getEntityMetadata($entityClass);
        $bundle      = $this->getContainer()->get('kernel')->getBundle($bundle);

        $questionHelper->writeSection($output, 'AdminList Generation');

        $generator = $this->getGenerator($this->getApplication()->getKernel()->getBundle("KunstmaanGeneratorBundle"));
        $generator->setQuestion($questionHelper);
        $generator->generate($bundle, $entityClass, $metadata[0], $output);

        $parts       = explode('\\', $entity);
        $entityClass = array_pop($parts);

        $this->updateRouting($questionHelper, $input, $output, $bundle, $entityClass);
    }

    /**
     * Interacts with the user.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();
        $questionHelper->writeSection($output, 'Welcome to the Kunstmaan admin list generator');

        // entity
        $entity = null;
        try {
            $entity = $input->getOption('entity') ? Validators::validateEntityName($input->getOption('entity')) : null;
        } catch (\Exception $error) {
            $output->writeln(
                $questionHelper->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error')
            );
        }

        if (is_null($entity)) {
            $output->writeln(
                array(
                    '',
                    'This command helps you to generate an admin list for your entity.',
                    '',
                    'You must use the shortcut notation like <comment>AcmeBlogBundle:Post</comment>.',
                    '',
                )
            );

            $question = new Question($questionHelper->getQuestion('The entity shortcut name', $entity), $entity);
            $question->setValidator(array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateEntityName'));
            $entity = $questionHelper->ask($input, $output, $question);
            $input->setOption('entity', $entity);
        }
    }

    /**
     * @param QuestionHelper  $questionHelper The question helper
     * @param InputInterface  $input          The command input
     * @param OutputInterface $output         The command output
     * @param Bundle          $bundle         The bundle
     * @param string          $entityClass    The classname of the entity
     *
     * @return void
     */
    protected function updateRouting(
        QuestionHelper $questionHelper,
        InputInterface $input,
        OutputInterface $output,
        Bundle $bundle,
        $entityClass
    ) {
        $auto      = true;
        $multilang = false;
        if ($input->isInteractive()) {
            $confirmationQuestion = new ConfirmationQuestion(
                $questionHelper->getQuestion('Is it a multilanguage site', 'yes', '?'), true
            );
            $multilang            = $questionHelper->ask($input, $output, $confirmationQuestion);
            $confirmationQuestion = new ConfirmationQuestion(
                $questionHelper->getQuestion('Do you want to update the routing automatically', 'yes', '?'), true
            );
            $auto                 = $questionHelper->ask($input, $output, $confirmationQuestion);
        }

        $prefix = $multilang ? '/{_locale}' : '';

        $code = sprintf("%s:\n", strtolower($bundle->getName()) . '_' . strtolower($entityClass) . '_admin_list');
        $code .= sprintf("    resource: @%s/Controller/%sAdminListController.php\n", $bundle->getName(), $entityClass);
        $code .= "    type:     annotation\n";
        $code .= sprintf("    prefix:   %s/admin/%s/\n", $prefix, strtolower($entityClass));
        if ($multilang) {
            $code .= "    requirements:\n";
            $code .= "         _locale: %requiredlocales%\n";
        }

        if ($auto) {
            $file    = $bundle->getPath() . '/Resources/config/routing.yml';
            $content = '';

            if (file_exists($file)) {
                $content = file_get_contents($file);
            } elseif (!is_dir($dir = dirname($file))) {
                mkdir($dir, 0777, true);
            }

            $content .= "\n";
            $content .= $code;

            if (false === file_put_contents($file, $content)) {
                $output->writeln(
                    $questionHelper->getHelperSet()->get('formatter')->formatBlock(
                        "Failed adding the content automatically",
                        'error'
                    )
                );
            } else {
                return;
            }
        }

        $output->writeln('Add the following to your routing.yml');
        $output->writeln('/*******************************/');
        $output->write($code);
        $output->writeln('/*******************************/');
    }

    /**
     * KunstmaanTestBundle_TestEntity:
     * resource: "@KunstmaanTestBundle/Controller/TestEntityAdminListController.php"
     * type:     annotation
     * prefix:   /{_locale}/admin/testentity/
     * requirements:
     * _locale: %requiredlocales%
     */

    protected function createGenerator()
    {
        return new AdminListGenerator(GeneratorUtils::getFullSkeletonPath('adminlist'));
    }
}
