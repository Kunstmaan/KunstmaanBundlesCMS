<?php

namespace Kunstmaan\SearchBundle\Command;

use Kunstmaan\SearchBundle\Configuration\SearchConfigurationChain;
use Kunstmaan\SearchBundle\Configuration\SearchConfigurationInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * Command to create the indexes
 *
 * It will load the SearchConfigurationChain and call the createIndex() method on each SearchConfiguration
 */
final class SetupIndexCommand extends Command
{
    /**
     * @var SearchConfigurationChain
     */
    private $configurationChain;

    public function __construct(SearchConfigurationChain $configurationChain)
    {
        parent::__construct();

        $this->configurationChain = $configurationChain;
    }

    protected function configure()
    {
        $this
            ->setName('kuma:search:setup')
            ->setDescription('Set up the index(es)');
    }

    /**
     * @return int|null null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        /**
         * @var string
         * @var SearchConfigurationInterface $searchConfiguration
         */
        foreach ($this->configurationChain->getConfigurations() as $alias => $searchConfiguration) {
            $languagesNotAnalyzed = $searchConfiguration->getLanguagesNotAnalyzed();
            if (\count($languagesNotAnalyzed) > 0) {
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

        return 0;
    }
}
