<?php

namespace Kunstmaan\SearchBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SearchIndexCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName("kuma:search:search")
            ->setDescription("Search the index")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sherlock = $this->getContainer()->get('kunstmaan_search.sherlock');
        $response = $sherlock->searchIndex();

        $output->writeln('Index searched');
        $output->writeln($response);
    }


}