<?php

namespace Kunstmaan\SearchBundle\Command;

use Kunstmaan\SearchBundle\Configuration\SearchConfigurationChain;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * Command to create the indexes
 *
 * It will load the SearchConfigurationChain and call the createIndex() method on each SearchConfiguration
 */
#[AsCommand(name: 'kuma:search:setup', description: 'Set up the index(es)')]
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        foreach ($this->configurationChain->getConfigurations() as $alias => $searchConfiguration) {
            $languagesNotAnalyzed = $searchConfiguration->getLanguagesNotAnalyzed();
            if (\count($languagesNotAnalyzed) > 0) {
                $question = new ChoiceQuestion(
                    sprintf('Languages analyzer is not available for: %s. Do you want continue?', implode(', ', $languagesNotAnalyzed)),
                    ['No', 'Yes']
                );
                $question->setErrorMessage('Answer %s is invalid.');
                if ($helper->ask($input, $output, $question) === 'No') {
                    return 0;
                }
            }

            $searchConfiguration->createIndex();
            $output->writeln('Index created : ' . $alias);
        }

        return 0;
    }
}
