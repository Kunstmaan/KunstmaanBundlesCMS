<?php

namespace Kunstmaan\SearchBundle\Command;

use Kunstmaan\SearchBundle\Configuration\SearchConfigurationInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * Command to create the indexes
 *
 * It will load the SearchConfigurationChain and call the createIndex() method on each SearchConfiguration
 */
class SetupIndexCommand extends ContainerAwareCommand
{
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
     * @return null|int  null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $searchConfigurationChain = $this->getContainer()->get('kunstmaan_search.search_configuration_chain');
        /**
         * @var string                       $alias
         * @var SearchConfigurationInterface $searchConfiguration
         */
        foreach ($searchConfigurationChain->getConfigurations() as $alias => $searchConfiguration) {

            if (count($searchConfiguration->checkAnalyzerLanguages()) > 0) {
                $question = new ChoiceQuestion(
                    sprintf('Languages analyzer is not available for: %s. Do you want continue?',
                        implode(', ', $searchConfiguration->checkAnalyzerLanguages())
                    ),
                    ['No', 'Yes']
                );
                $question->setErrorMessage('Answer %s is invalid.');
                if ( $helper->ask($input, $output, $question) === 'no' ) {
                    return;
                }
            }

            $searchConfiguration->createIndex();
            $output->writeln('Index created : ' . $alias);
        }
    }
}
