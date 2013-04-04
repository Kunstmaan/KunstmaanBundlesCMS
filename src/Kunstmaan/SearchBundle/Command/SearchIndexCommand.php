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
            ->addOption('tag', null, InputOption::VALUE_OPTIONAL, 'The tag')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $query = $input->getOption('query');
        $tag = $input->getOption('tag');

        $sherlock = $this->getContainer()->get('kunstmaan_search.sherlock');
        $response = $sherlock->searchIndex($query, $tag);

        $output->writeln('Index searched');
    }


}