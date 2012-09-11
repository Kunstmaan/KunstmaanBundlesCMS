<?php

namespace Kunstmaan\AdminNodeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Kunstmaan\AdminNodeBundle\Entity\AclChangeset;
use Kunstmaan\AdminBundle\Permission\PermissionAdmin;

/**
 */
class ApplyAclCommand extends ContainerAwareCommand
{

    /**
     * @var EntityManager
     */
    private $em = null;

    /**
     * @var \Kunstmaan\AdminNodeBundle\Helper\ShellHelper
     */
    private $shellHelper = null;

    /**
     * @var Node
     */
    private $rootNode;

    protected function configure()
    {
        parent::configure();

        $this->setName('kuma:acl:apply')
            ->setDescription('Apply ACL changeset.')
            ->setHelp("The <info>kuma:acl:apply</info> can be used to apply an ACL changeset recursively, changesets are fetched from the database.")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->shellHelper = $this->getContainer()->get('kunstmaan.shell_helper');
        /* @var PermissionAdmin $permissionAdmin */
        $permissionAdmin = $this->getContainer()->get('kunstmaan_admin.permissionadmin');

        // Check if another ACL apply process is currently running & do nothing if it is
        if ($this->isRunning()) return;
        $aclRepo = $this->em->getRepository('KunstmaanAdminNodeBundle:AclChangeset');
        do {
            /**
             * @var AclChangeset $changeset
             */
            $changeset = $aclRepo->findNewChangeset();
            if (is_null($changeset)) {
                break;
            }
            $changeset->setPid(getmypid());
            $changeset->setStatus(AclChangeset::STATUS_RUNNING);
            $this->em->persist($changeset);
            $this->em->flush();

            $permissionAdmin->applyAclChangeset($changeset->getNode(), $changeset->getChangeset());

            $changeset->setStatus(AclChangeset::STATUS_FINISHED);
            $this->em->persist($changeset);
            $this->em->flush();

            $hasPending = $aclRepo->hasPendingChangesets();
        } while ($hasPending);
    }

    private function isRunning()
    {
        // Check if we have records in running state, if so read PID & check if process is active
        /**
         * @var AclChangeset
         */
        $runningAclChangeset = $this->em->getRepository('KunstmaanAdminNodeBundle:AclChangeset')->findRunningChangeset();
        if (!is_null($runningAclChangeset)) {
            // Found running process, check if PID is still running
            if (!$this->shellHelper->isProcessRunning($runningAclChangeset->getPid())) {
                // PID not running, process probably failed...
                $runningAclChangeset->setStatus(AclChangeset::STATUS_FAILED);
                $this->em->persist($runningAclChangeset);
                $this->em->flush();
            }
        }

        return false;
    }

}
