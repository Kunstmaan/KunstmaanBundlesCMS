<?php

namespace Kunstmaan\AdminNodeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Kunstmaan\AdminBundle\Entity\Group;

class ConvertSequenceNumberToWeightCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('kuma:nodes:convertsequencenumbertoweight')
            ->setDescription('Set all the nodetranslations weights based on the nodes sequencenumber')
            ->setHelp("The <info>AdminNode:nodetranslations:updateweights</info> will loop over all nodetranslation and set their weight based on the nodes sequencenumber.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('xdebug.max_nesting_level', 150);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $batchSize = 20;
        $i =0;
        $q = $em->createQuery('SELECT t FROM Kunstmaan\AdminNodeBundle\Entity\NodeTranslation t WHERE t.weight IS NULL');
        
        $iterableResult = $q->iterate(); 
        
        while (($row = $iterableResult->next()) !== false){
            $nodeTranslation = $row[0];
            if ($nodeTranslation->getWeight() == null) {
                $output->writeln('- editing node: '. $nodeTranslation->getTitle());
                $nodeTranslation->setWeight($nodeTranslation->getNode()->getSequencenumber());
                $em->persist($nodeTranslation);

                ++$i;
            }
            if (($i % $batchSize) == 0) {
                $output->writeln('FLUSHING!');
                $em->flush(); 
                $em->clear();
            }
        }
        
        $output->writeln('FLUSHING!'); 
            $em->flush(); 
            $em->clear(); 

        $output->writeln('Updated all nodes');
    }
}
