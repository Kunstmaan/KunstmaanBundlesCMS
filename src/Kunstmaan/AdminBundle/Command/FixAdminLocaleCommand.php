<?php

namespace Kunstmaan\AdminBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Symfony CLI command to set admin locale
 */
class FixAdminLocaleCommand extends ContainerAwareCommand
{
    /**
     * Configures the command.
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('kuma:fix:admin-locale')
            ->setDescription('Set the admin locale for all users to the default admin locale.')
            ->setHelp('The <info>kuma:fix:admin-locale</info> command can be used to set the admin locale for all users to the default admin locale.');
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
        $defaultAdminLocale = $this->getContainer()->getParameter('kunstmaan_admin.default_admin_locale');
        foreach ($users as $user) {
            $user->setAdminLocale($defaultAdminLocale);
            $em->persist($user);
        }
        $em->flush();
        $output->writeln('<info>The default admin locale was successfully set for all users.</info>');
    }
}
