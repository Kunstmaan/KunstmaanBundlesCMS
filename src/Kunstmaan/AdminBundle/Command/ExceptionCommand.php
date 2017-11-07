<?php

namespace Kunstmaan\AdminBundle\Command;

use Kunstmaan\AdminBundle\Entity\Exception;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExceptionCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('kuma:exception:clear')
            ->setDescription('Remove mark exception based on days.')
            ->setDefinition(
                [
                    new InputArgument('days', InputArgument::OPTIONAL, 'Days', 7)
                ]
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $days = (int) $input->getArgument('days');
        if ( $days <= 0 ) {
            $output->writeln('<bg=red;options=bold>Days number must be higher than 0</>');
        }

        $nowDate = new \DateTime();
        $convertDate = $nowDate->sub(
            new \DateInterval('P'.$days.'D')
        );

        $cp = 0;
        $exceptions = $em->getRepository(Exception::class)->findAllHigherThanDays($convertDate);
        if ( $exceptions ) {
            foreach ( $exceptions as $exception ) {
                $em->remove($exception);
                $cp++;
            }
            $em->flush();
        }
        
        $output->writeln(sprintf('Removed exceptions <comment>%s</comment>', $cp));
    }
}