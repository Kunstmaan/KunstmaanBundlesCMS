<?php

namespace Kunstmaan\SearchBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SearchIndexCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName("kuma:search:search")
            ->setDescription("Search the index")
            ->addOption('query', null, InputOption::VALUE_REQUIRED, 'The query to perform')
            ->addOption('tag', null, InputOption::VALUE_OPTIONAL, 'The tags, comma seperated')
            ->addOption('type', null, InputOption::VALUE_OPTIONAL, 'The type of page you want to search on')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $query = $input->getOption('query');
        $tag = $input->getOption('tag');
        $type = $input->getOption('type');

        $sherlock = $this->getContainer()->get('kunstmaan_search.searchprovider.sherlock');
        $response = $sherlock->searchIndex($query, $type, $tag);

        foreach($response as $hit)
        {
            echo $hit['score'].' : '.$hit['source']['title'].' ['.$hit['source']['lang'].']  ('.$hit['source']['slug'] .") \r\n";
        }

        $responseData = $response->responseData;
        foreach($responseData['facets'] as $facet)
        {
            foreach($facet['terms'] as $term)
            {
                echo implode(' : ',$term) . "\r\n";
            }
        }

        $output->writeln('Index searched');
    }

}