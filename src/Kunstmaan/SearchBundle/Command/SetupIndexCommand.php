<?php

namespace Kunstmaan\SearchBundle\Command;


use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupIndexCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName("kuma:search:setup")
            ->setDescription("Set up the index")
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $responses = array();
        $searchConfigurationChain = $this->getContainer()->get('kunstmaan_search.searchconfiguration_chain');
        foreach($searchConfigurationChain->getSearchConfigurations() as $searchConfiguration){
            $responses[] = array($searchConfiguration, $searchConfiguration->create());
        }

        foreach($responses as $response){
            $output->writeln('Index created : ' . ClassLookup::getClass($response[0]) . ' : ' . $response[1]);
        }
    }


}