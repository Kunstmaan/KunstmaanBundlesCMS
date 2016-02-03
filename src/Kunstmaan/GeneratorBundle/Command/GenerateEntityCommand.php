<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\Console\Input\ArrayInput;
use Kunstmaan\GeneratorBundle\Generator\DoctrineEntityGenerator;
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCommand;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * GenerateEntityCommand
 */
class GenerateEntityCommand extends GenerateDoctrineCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('kuma:generate:entity')
            ->setDescription('Generates a new Doctrine entity inside a bundle')
            ->addOption('entity', null, InputOption::VALUE_REQUIRED, 'The entity class name to initialize (shortcut notation)')
            ->addOption('fields', null, InputOption::VALUE_REQUIRED, 'The fields to create with the new entity')
            ->addOption('prefix', '', InputOption::VALUE_OPTIONAL, 'The prefix to be used in the table names of the generated entities')
            ->addOption('with-repository', null, InputOption::VALUE_NONE, 'Whether to generate the entity repository or not')
            ->addOption('with-adminlist', null, InputOption::VALUE_NONE, 'Whether to generate the entity AdminList or not')
            ->setHelp(<<<EOT
The <info>kuma:generate:entity</info> task generates a new Doctrine
entity inside a bundle:

<info>php app/console kuma:generate:entity --entity=AcmeBlogBundle:Blog/Post</info>

The above command would initialize a new entity in the following entity
namespace <info>Acme\BlogBundle\Entity\Blog\Post</info>.

You can also optionally specify the fields you want to generate in the new
entity:

<info>php app/console kuma:generate:entity --entity=AcmeBlogBundle:Blog/Post --fields="title:string(255) body:text"</info>

The command can also generate the corresponding entity repository class with the
<comment>--with-repository</comment> option:

<info>php app/console kuma:generate:entity --entity=AcmeBlogBundle:Blog/Post --with-repository</info>

The command can also generate the corresponding entity AdminList with the
<comment>--with-adminlist</comment> option:

<info>php app/console kuma:generate:entity --entity=AcmeBlogBundle:Blog/Post --with-adminlist</info>

Use the <info>--prefix</info> option to add a prefix to the table names of the generated entities

<info>php app/console kuma:generate:entity --entity=AcmeBlogBundle:Blog/Post --prefix=demo_</info>

To deactivate the interaction mode, simply use the `--no-interaction` option
without forgetting to pass all needed options:

<info>php app/console kuma:generate:entity --entity=AcmeBlogBundle:Blog/Post --fields="title:string(255) body:text" --with-repository --with-adminlist --no-interaction</info>
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    $questionHelper = $this->getQuestionHelper();

        if ($input->isInteractive()) {
            $confirmationQuestion = new ConfirmationQuestion($questionHelper->getQuestion('Do you confirm generation', 'yes', '?'), true);
            if (!$questionHelper->ask($input, $output, $confirmationQuestion)) {
                $output->writeln('<error>Command aborted</error>');

                return 1;
            }
        }

        GeneratorUtils::ensureOptionsProvided($input, array('entity', 'fields', 'prefix'));

        $entityInput = Validators::validateEntityName($input->getOption('entity'));
        list($bundleName, $entity) = $this->parseShortcutNotation($entityInput);
        $format = 'annotation';
        $fields = $this->parseFields($input->getOption('fields'));

        $prefix = $input->getOption('prefix');

        $questionHelper->writeSection($output, 'Entity generation');

        $bundle = $this->getContainer()->get('kernel')->getBundle($bundleName);

        $generator = $this->getGenerator($this->getApplication()->getKernel()->getBundle("KunstmaanGeneratorBundle"));
        $generator->generate($bundle, $entity, $format, array_values($fields), $input->getOption('with-repository'), $prefix);

        $output->writeln('Generating the entity code: <info>OK</info>');

        $withAdminlist = $input->getOption('with-adminlist');
        if ($withAdminlist) {
            $command = $this->getApplication()->find('kuma:generate:adminlist');
            $arguments = array(
                'command' => 'doctrine:fixtures:load',
                '--entity' => $entityInput
            );
            $input = new ArrayInput($arguments);
            $command->run($input, $output);
        }

        $questionHelper->writeGeneratorSummary($output, array());

        $output->writeln(array(
                'Make sure you update your database first before you test the entity/adminlist:',
                '    Directly update your database:          <comment>app/console doctrine:schema:update --force</comment>',
                '    Create a Doctrine migration and run it: <comment>app/console doctrine:migrations:diff && app/console doctrine:migrations:migrate</comment>',
                '')
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();
        $questionHelper->writeSection($output, 'Welcome to the Doctrine2 entity generator');

        // namespace
        $output->writeln(array(
            '',
            'This command helps you generate Doctrine2 entities.',
            '',
            'First, you need to give the entity name you want to generate.',
            'You must use the shortcut notation like <comment>AcmeBlogBundle:Post</comment>.',
            ''
        ));

        $bundleNames = array_keys($this->getContainer()->get('kernel')->getBundles());
        /** @var $foundBundle Bundle */
        $foundBundle = $bundle = $entity = null;

        while (true) {
            $question = new Question($questionHelper->getQuestion('The Entity shortcut name', $input->getOption('entity')));
            $question->setValidator(array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateEntityName'));
            $question->setAutocompleterValues($bundleNames);
            $entity = $questionHelper->ask($input, $output, $question);

            list($bundle, $entity) = $this->parseShortcutNotation($entity);

            // check entity name
            if(!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $entity)) {
                $output->writeln(sprintf('<bg=red> "%s" is not a valid entity name.</>', $entity));
                continue;
            }

            // check reserved words
            if ($this->getGenerator()->isReservedKeyword($entity)) {
                $output->writeln(sprintf('<bg=red> "%s" is a reserved word</>.', $entity));
                continue;
            }

            try {
                $foundBundle = $this->getContainer()->get('kernel')->getBundle($bundle);

                if (!file_exists($foundBundle->getPath().'/Entity/'.str_replace('\\', '/', $entity).'.php')) {
                    break;
                }

                $output->writeln(sprintf('<bg=red>Entity "%s:%s" already exists</>.', $bundle, $entity));
            } catch (\Exception $e) {
                $output->writeln(sprintf('<bg=red>Bundle "%s" does not exist.</>', $bundle));
            }
        }
        $input->setOption('entity', $bundle.':'.$entity);

        $inputAssistant = GeneratorUtils::getInputAssistant($input, $output, $questionHelper, $this->getApplication()->getKernel(), $this->getContainer());
        $inputAssistant->askForPrefix(null, $foundBundle->getNamespace());

        // fields
        $input->setOption('fields', $this->addFields($input, $output, $questionHelper));

        // repository?
        $output->writeln('');
        $confirmationQuestion = new ConfirmationQuestion($questionHelper->getQuestion('Do you want to generate an empty repository class', $input->getOption('with-repository') ? 'yes' : 'no', '?'), $input->getOption('with-repository'));
        $withRepository = $questionHelper->ask($input, $output, $confirmationQuestion);
        $input->setOption('with-repository', $withRepository);

        // repository?
        $output->writeln('');
        $confirmationQuestion = new ConfirmationQuestion($questionHelper->getQuestion('Do you want to generate an AdminList for your entity', $input->getOption('with-adminlist') ? 'yes' : 'no', '?'), $input->getOption('with-adminlist'));
        $withAdminList = $questionHelper->ask($input, $output, $confirmationQuestion);
        $input->setOption('with-adminlist', $withAdminList);

        // summary
        $output->writeln(array(
            '',
            $this->getHelper('formatter')->formatBlock('Summary before generation', 'bg=blue;fg=white', true),
            '',
            sprintf("You are going to generate a \"<info>%s:%s</info>\" Doctrine2 entity", $bundle, $entity),
            '',
        ));
    }

    /**
     * @param array|string $input
     *
     * @return array[]
     */
    private function parseFields($input)
    {
        if (is_array($input)) {
            return $input;
        }

        $fields = array();
        foreach (explode(' ', $input) as $value) {
            $elements = explode(':', $value);
            $name = $elements[0];
            if (strlen($name)) {
                $type = isset($elements[1]) ? $elements[1] : 'string';
                preg_match_all('/(.*)\((.*)\)/', $type, $matches);
                $type = isset($matches[1][0]) ? $matches[1][0] : $type;
                $length = isset($matches[2][0]) ? $matches[2][0] : null;

                $fields[$name] = array('fieldName' => $name, 'type' => $type, 'length' => $length);
            }
        }

        return $fields;
    }

    /**
     * @param InputInterface  $input          The input
     * @param OutputInterface $output         The output
     * @param QuestionHelper  $questionHelper The question helper
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    private function addFields(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper)
    {
        $fields = $this->parseFields($input->getOption('fields'));
        $output->writeln(array(
            '',
            'Instead of starting with a blank entity, you can add some fields now.',
            'Note that the primary key will be added automatically (named <comment>id</comment>).',
            '',
        ));
        $output->write('<info>Available types:</info> ');

        $types = array_keys(Type::getTypesMap());
        $count = 20;
        foreach ($types as $i => $type) {
            if ($count > 50) {
                $count = 0;
                $output->writeln('');
            }
            $count += strlen($type);
            $output->write(sprintf('<comment>%s</comment>', $type));
            if (count($types) != $i + 1) {
                $output->write(', ');
            } else {
                $output->write('.');
            }
        }
        $output->writeln('');

        $fieldValidator = function ($type) use ($types) {
            if (!in_array($type, $types)) {
                throw new \InvalidArgumentException(sprintf('Invalid type "%s".', $type));
            }

            return $type;
        };

        $lengthValidator = function ($length) {
            if (!$length) {
                return $length;
            }

            $result = filter_var($length, FILTER_VALIDATE_INT, array(
                'options' => array('min_range' => 1)
            ));

            if (false === $result) {
                throw new \InvalidArgumentException(sprintf('Invalid length "%s".', $length));
            }

            return $length;
        };

        while (true) {
            $output->writeln('');
            $generator = $this->getGenerator();
            $question = new Question($questionHelper->getQuestion('New field name (enter empty name to stop adding fields)', null));
            $question->setValidator(function ($name) use ($fields, $generator) {
                if (isset($fields[$name]) || 'id' == $name) {
                    throw new \InvalidArgumentException(sprintf('Field "%s" is already defined.', $name));
                }

                // check reserved words
                if ($generator->isReservedKeyword($name)) {
                    throw new \InvalidArgumentException(sprintf('Name "%s" is a reserved word.', $name));
                }

                return $name;
            });
            $columnName = $questionHelper->ask($input, $output, $question);
            if (!$columnName) {
                break;
            }

            $defaultType = 'string';

            // try to guess the type by the column name prefix/suffix
            if (substr($columnName, -3) == '_at') {
                $defaultType = 'datetime';
            } elseif (substr($columnName, -3) == '_id') {
                $defaultType = 'integer';
            } elseif (substr($columnName, 0, 3) == 'is_') {
                $defaultType = 'boolean';
            } elseif (substr($columnName, 0, 4) == 'has_') {
                $defaultType = 'boolean';
            }

            $columnName = DoctrineEntityGenerator::convertCamelCaseToSnakeCase($columnName);

            $question = new Question($questionHelper->getQuestion('Field type', $defaultType), $defaultType);
            $question->setAutocompleterValues($types);
            $question->setValidator($fieldValidator);
            $type = $questionHelper->ask($input, $output, $question);

            $data = array('columnName' => $columnName, 'fieldName' => lcfirst(Container::camelize($columnName)), 'type' => $type);

            if ($type == 'string') {
                $question = new Question($questionHelper->getQuestion('Field length', 255), 255);
                $question->setValidator($lengthValidator);
                $data['length'] = $questionHelper->ask($input, $output, $question);
            }

            $fields[$columnName] = $data;
        }

        return $fields;
    }

    protected function createGenerator()
    {
        return new DoctrineEntityGenerator($this->getContainer()->get('filesystem'), $this->getContainer()->get('doctrine'));
    }
}
