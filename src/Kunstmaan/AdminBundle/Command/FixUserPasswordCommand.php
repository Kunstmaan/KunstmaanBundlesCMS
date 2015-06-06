<?php

namespace Kunstmaan\AdminBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixUserPasswordCommand extends ContainerAwareCommand
{

    /**
     * Configures the command.
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('kuma:fix:user-password')
            ->setDescription('Set the password changed value to true for all users.')
            ->setHelp('The <info>kuma:fix:user-password</info> command can be used to set password changed for all users to true.');
    }

    /**
     * @param InputInterface $input The input
     * @param OutputInterface $output The output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        /* @var EntityRepository $repo */
        $repo = $em->getRepository('KunstmaanAdminBundle:User');
        $users = $repo->findAll();
        foreach ($users as $user) {
            $user->setPasswordChanged(1);
            $em->persist($user);
        }
        $em->flush();
        $output->writeln('<info>The password changed value has been set to true successfully for all users.</info>');
    }

}