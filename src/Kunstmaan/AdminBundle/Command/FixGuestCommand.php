<?php

namespace Kunstmaan\AdminBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Symfony CLI command to remove the ROLE_GUEST dependency
 */
class FixGuestCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager $em
     */
    private $em = null;

    /**
     * Configures the command.
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('kuma:fix:guest')
            ->setDescription('Remove the ROLE_GUEST dependency.')
            ->setHelp("The <info>kuma:fix:guest</info> command can be used to remove the ROLE_GUEST dependency.");
    }

    /**
     * Modify ROLE_GUEST (if it exists)
     *
     * @param InputInterface $input The input
     * @param OutputInterface $output The output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setEntityManager($this->getContainer()->get('doctrine.orm.entity_manager'));

        if ($this->isRoleInUse('IS_AUTHENTICATED_ANONYMOUSLY')) {
            $output->writeln(
                '<error>The IS_AUTHENTICATED_ANONYMOUSLY role is already in use : you\'re on your own!</error>'
            );

            return 1;
        }

        /* @var EntityRepository $repo */
        $repo = $this->em->getRepository('KunstmaanAdminBundle:Role');
        $guestRole = $repo->findOneByRole('ROLE_GUEST');
        if (!is_null($guestRole)) {
            try {
                $guestRole->setRole('IS_AUTHENTICATED_ANONYMOUSLY');
                $this->em->persist($guestRole);
                $this->em->flush();

                // ACL security identities
                $sql = 'UPDATE acl_security_identities SET identifier=? WHERE identifier=?';
                $this->em->getConnection()->executeUpdate($sql, array('IS_AUTHENTICATED_ANONYMOUSLY', 'ROLE_GUEST'));

                $output->writeln('<info>The ROLE_GUEST dependency was successfully removed.</info>');
            } catch (Exception $e) {
                $output->writeln(
                    '<error>A fatal error occurred while trying to remove the ROLE_GUEST dependency.</error>'
                );
                $output->writeln(array('<error>Error : ', $e->getMessage(), '</error>'));
            }
        } else {
            $output->writeln('<error>ROLE_GUEST not found : you\'re on your own!</error>');
        }
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Check if the specified role is in use, both in the roles and the acl security identities tables
     *
     * @param $roleName
     *
     * @return bool
     */
    private function isRoleInUse($roleName)
    {
        /* @var EntityRepository $repo */
        $repo = $this->em->getRepository('KunstmaanAdminBundle:Role');
        $role = $repo->findOneByRole($roleName);
        $sql = 'SELECT id FROM acl_security_identities WHERE identifier=?';
        $stmt = $this->em->getConnection()->executeQuery($sql, array($roleName));
        $aclIdentity = $stmt->fetch();

        return !is_null($role) || ($aclIdentity !== false);
    }
}
