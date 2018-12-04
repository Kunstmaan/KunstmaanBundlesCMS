<?php

namespace Kunstmaan\SearchBundle\Command;

use Kunstmaan\SearchBundle\Configuration\SearchConfigurationChain;
use Kunstmaan\SearchBundle\Configuration\SearchConfigurationInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * Command to create the indexes
 *
 * It will load the SearchConfigurationChain and call the createIndex() method on each SearchConfiguration
 *
 * @final since 5.1
 * NEXT_MAJOR extend from `Command` and remove `$this->getContainer` usages
 */
class SetupIndexCommand extends ContainerAwareCommand
{
    /**
     * @var SearchConfigurationChain
     */
    private $configurationChain;

    public function __construct(/* SearchConfigurationChain */ $configurationChain = null)
    {
        parent::__construct();

        if (!$configurationChain instanceof SearchConfigurationChain) {
            @trigger_error(sprintf('Passing a command name as the first argument of "%s" is deprecated since version symfony 3.4 and will be removed in symfony 4.0. If the command was registered by convention, make it a service instead. ', __METHOD__), E_USER_DEPRECATED);

            $this->setName(null === $configurationChain ? 'kuma:search:setup' : $configurationChain);

            return;
        }

        $this->configurationChain = $configurationChain;
    }

    protected function configure()
    {
        $this
            ->setName('kuma:search:setup')
            ->setDescription('Set up the index(es)');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        if (null === $this->configurationChain) {
            $this->configurationChain = $this->getContainer()->get('kunstmaan_search.search_configuration_chain');
        }

        /**
         * @var string
         * @var SearchConfigurationInterface $searchConfiguration
         */
        foreach ($this->configurationChain->getConfigurations() as $alias => $searchConfiguration) {
            $languagesNotAnalyzed = $searchConfiguration->getLanguagesNotAnalyzed();
            if (count($languagesNotAnalyzed) > 0) {
                $question = new ChoiceQuestion(
                    sprintf('Languages analyzer is not available for: %s. Do you want continue?', implode(', ', $languagesNotAnalyzed)),
                    ['No', 'Yes']
                );
                $question->setErrorMessage('Answer %s is invalid.');
                if ($helper->ask($input, $output, $question) === 'No') {
                    return;
                }
            }

            $searchConfiguration->createIndex();
            $output->writeln('Index created : ' . $alias);
        }
    }
}
