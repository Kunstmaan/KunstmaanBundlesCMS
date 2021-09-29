<?php

namespace Kunstmaan\AdminBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ExceptionCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function configure()
    {
        parent::configure();

        $this->setName('kuma:exception:clear')
            ->setDescription('Remove resolved exceptions based on days.')
            ->setDefinition(
                [
                    new InputArgument('days', InputArgument::OPTIONAL, 'Days', 7),
                ]
            );
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $days = (int) $input->getArgument('days');
        if ($days <= 0) {
            $output->writeln('<bg=red;options=bold>Days number must be higher than 0</>');
        }

        $nowDate = new \DateTime();
        $convertDate = $nowDate->sub(
            new \DateInterval('P' . $days . 'D')
        );

        $cp = 0;
        $exceptions = $this->em->getRepository(Exception::class)->findAllHigherThanDays($convertDate);
        if ($exceptions) {
            foreach ($exceptions as $exception) {
                $this->em->remove($exception);
                ++$cp;
            }
            $this->em->flush();
        }

        $output->writeln(sprintf('Removed exceptions <comment>%s</comment>', $cp));

        return 0;
    }
}
