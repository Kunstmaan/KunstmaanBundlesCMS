<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Generator\AdminListGenerator;
use Kunstmaan\GeneratorBundle\Helper\EntityValidator;
use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Kunstmaan\GeneratorBundle\Helper\Sf4AppBundle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * @internal
 */
class GenerateAdminListCommand extends AbstractGeneratorCommand
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setDefinition(
                [
                    new InputOption(
                        'entity',
                        '',
                        InputOption::VALUE_REQUIRED,
                        'The entity class name to create an admin list for (shortcut notation)'
                    ),
                    new InputOption(
                        'sortfield',
                        '',
                        InputOption::VALUE_OPTIONAL,
                        'The name of the sort field if entity needs to be sortable'
                    ),
                ]
            )
            ->setDescription('Generates a KunstmaanAdminList')
            ->setHelp(
                <<<'EOT'
                The <info>kuma:generate:adminlist</info> command generates an AdminList for a Doctrine ORM entity.

<info>php bin/console kuma:generate:adminlist Bundle:Entity</info>
EOT
            )
            ->setName('kuma:generate:adminlist');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $questionHelper = $this->getQuestionHelper();

        GeneratorUtils::ensureOptionsProvided($input, ['entity']);

        $entity = EntityValidator::validate($input->getOption('entity'));
        $entityClass = $entity;
        $em = $this->getContainer()->get('doctrine')->getManager();

        $metadata = $em->getClassMetadata($entityClass);
        $bundle = new Sf4AppBundle($this->getContainer()->getParameter('kernel.project_dir'));

        $questionHelper->writeSection($output, 'AdminList Generation');

        $generator = $this->getGenerator($this->getApplication()->getKernel()->getBundle('KunstmaanGeneratorBundle'));
        $generator->setQuestion($questionHelper);
        $generator->generate($bundle, $entityClass, $metadata, $output, $input->getOption('sortfield'));

        return 0;
    }

    /**
     * @return void
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();
        $questionHelper->writeSection($output, 'Welcome to the Kunstmaan admin list generator');

        // entity
        $entity = null;

        try {
            $entity = $input->getOption('entity') ? EntityValidator::validate($input->getOption('entity')) : null;
        } catch (\Exception $error) {
            $output->writeln(
                $questionHelper->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error')
            );
        }

        $message = 'You must use the FQCN like <comment>\App\Entity\Post</comment>.';

        if (is_null($entity)) {
            $output->writeln(
                [
                    '',
                    'This command helps you to generate an admin list for your entity.',
                    '',
                    $message,
                    '',
                ]
            );

            $message = 'The entity FQCN';

            $question = new Question($questionHelper->getQuestion($message, $entity), $entity);
            $question->setValidator(['\Kunstmaan\GeneratorBundle\Helper\EntityValidator', 'validate']);
            $entity = $questionHelper->ask($input, $output, $question);
            $input->setOption('entity', $entity);

            $question = new Question($questionHelper->getQuestion('The name of the sort field if entity needs to be sortable', false, '?'), false);
            $sortfield = $questionHelper->ask($input, $output, $question);
            $input->setOption('sortfield', $sortfield);
        }
    }

    protected function createGenerator()
    {
        return new AdminListGenerator(GeneratorUtils::getFullSkeletonPath('adminlist'));
    }
}
