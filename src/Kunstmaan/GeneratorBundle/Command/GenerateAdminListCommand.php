<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
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
            ->setDefinition(array(new InputOption('entity', '', InputOption::VALUE_REQUIRED, 'The entity class name to create an admin list for (shortcut notation)'),))
            ->setDescription('Generates a KunstmaanAdminList')
            ->setHelp(<<<EOT
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
        $dialog = $this->getDialogHelper();

        GeneratorUtils::ensureOptionsProvided($input, array('entity'));

        $entity = Validators::validateEntityName($input->getOption('entity'));
        list($bundle, $entity) = $this->parseShortcutNotation($entity);

        $entityClass = $this->getContainer()->get('doctrine')->getAliasNamespace($bundle).'\\'.$entity;
        $metadata    = $this->getEntityMetadata($entityClass);
        $bundle      = $this->getContainer()->get('kernel')->getBundle($bundle);

        $dialog->writeSection($output, 'AdminList Generation');

        $generator = $this->getGenerator($this->getApplication()->getKernel()->getBundle("KunstmaanGeneratorBundle"));
        $generator->setDialog($dialog);
        $generator->generate($bundle, $entityClass, $metadata[0], $output);

        $parts = explode('\\', $entity);
        $entityClass = array_pop($parts);

        $this->updateRouting($dialog, $input, $output, $bundle, $entityClass);
    }

    /**
     * Interacts with the user.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Welcome to the Kunstmaan admin list generator');

        // entity
        $entity = null;
        try {
            $entity = $input->getOption('entity') ? Validators::validateEntityName($input->getOption('entity')) : null;
        } catch (\Exception $error) {
            $output->writeln($dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }

        if (is_null($entity)) {
            $output->writeln(array(
                '',
                'This command helps you to generate an admin list for your entity.',
                '',
                'You must use the shortcut notation like <comment>AcmeBlogBundle:Post</comment>.',
                '',
            ));

            $entity = $dialog->askAndValidate($output, $dialog->getQuestion('The entity shortcut name', $entity), array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateEntityName'), false, $entity);
            $input->setOption('entity', $entity);
        }
    }

    /**
     * @param DialogHelper    $dialog      The dialog helper
     * @param InputInterface  $input       The command input
     * @param OutputInterface $output      The command output
     * @param Bundle          $bundle      The bundle
     * @param string          $entityClass The classname of the entity
     *
     * @return void
     */
    protected function updateRouting(DialogHelper $dialog, InputInterface $input, OutputInterface $output, Bundle $bundle, $entityClass)
    {
        $auto = true;
        $multilang = false;
        if ($input->isInteractive()) {
            $multilang = $dialog->askConfirmation($output, $dialog->getQuestion('Is it a multilanguage site', 'yes', '?'), true);
            $auto = $dialog->askConfirmation($output, $dialog->getQuestion('Do you want to update the routing automatically', 'yes', '?'), true);
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
            $file = $bundle->getPath() . '/Resources/config/routing.yml';
            $content = '';

            if (file_exists($file)) {
                $content = file_get_contents($file);
            } elseif (!is_dir($dir = dirname($file))) {
                mkdir($dir, 0777, true);
            }

            $content .= "\n";
            $content .= $code;

            if (false === file_put_contents($file, $content)) {
                $output->writeln($dialog->getHelperSet()->get('formatter')->formatBlock("Failed adding the content automatically", 'error'));
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
    resource: "@KunstmaanTestBundle/Controller/TestEntityAdminListController.php"
    type:     annotation
    prefix:   /{_locale}/admin/testentity/
    requirements:
    _locale: %requiredlocales%
     */

    protected function createGenerator()
    {
        return new AdminListGenerator($this->getContainer()->get('filesystem'), GeneratorUtils::getFullSkeletonPath('adminlist'));
    }
}
