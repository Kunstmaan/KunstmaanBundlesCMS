<?php

namespace Kunstmaan\AdminBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\Exception;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @final since 5.1
 * NEXT_MAJOR extend from `Command` and remove `$this->getContainer` usages
 */
class ExceptionCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface|null $em
     */
    public function __construct(/* EntityManagerInterface */ $em = null)
    {
        parent::__construct();

        if (!$em instanceof EntityManagerInterface) {
            @trigger_error(sprintf('Passing a command name as the first argument of "%s" is deprecated since version symfony 3.4 and will be removed in symfony 4.0. If the command was registered by convention, make it a service instead. ', __METHOD__), E_USER_DEPRECATED);

            $this->setName(null === $em ? 'kuma:exception:clear' : $em);

            return;
        }

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
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null === $this->em) {
            $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        }

        $days = (int) $input->getArgument('days');
        if ($days <= 0) {
            $output->writeln('<bg=red;options=bold>Days number must be higher than 0</>');
        }

        $nowDate = new \DateTime();
        $convertDate = $nowDate->sub(
            new \DateInterval('P'.$days.'D')
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
    }
}
