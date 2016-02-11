<?php

namespace Kunstmaan\AdminBundle\Command;

use Doctrine\ORM\EntityManager;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Kunstmaan\AdminBundle\Entity\AclChangeset;
use Kunstmaan\AdminBundle\Repository\AclChangesetRepository;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin;
use Kunstmaan\UtilitiesBundle\Helper\Shell\Shell;

/**
 * Symfony CLI command to apply the {@link AclChangeSet} with status {@link AclChangeSet::STATUS_NEW} to their entities
 */
class ApplyAclCommand extends ContainerAwareCommand
{

    /**
     * @var EntityManager
     */
    private $em = null;

    /**
     * @var Shell
     */
    private $shellHelper = null;

    /**
     * Configures the command.
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('kuma:acl:apply')
             ->setDescription('Apply ACL changeset.')
             ->setHelp("The <info>kuma:acl:apply</info> can be used to apply an ACL changeset recursively, changesets are fetched from the database.");
    }

    /**
     * Apply the {@link AclChangeSet} with status {@link AclChangeSet::STATUS_NEW} to their entities
     *
     * @param InputInterface  $input  The input
     * @param OutputInterface $output The output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->shellHelper = $this->getContainer()->get('kunstmaan_utilities.shell');
        /* @var PermissionAdmin $permissionAdmin */
        $permissionAdmin = $this->getContainer()->get('kunstmaan_admin.permissionadmin');

        // Check if another ACL apply process is currently running & do nothing if it is
        if ($this->isRunning()) {
            return;
        }
        /* @var AclChangesetRepository $aclRepo */
        $aclRepo = $this->em->getRepository('KunstmaanAdminBundle:AclChangeset');
        do {
            /* @var AclChangeset $changeset */
            $changeset = $aclRepo->findNewChangeset();
            if (is_null($changeset)) {
                break;
            }
            $changeset->setPid(getmypid());
            $changeset->setStatus(AclChangeset::STATUS_RUNNING);
            $this->em->persist($changeset);
            $this->em->flush($changeset);

            $entity = $this->em->getRepository($changeset->getRefEntityName())->find($changeset->getRefId());
            $permissionAdmin->applyAclChangeset($entity, $changeset->getChangeset());

            $changeset->setStatus(AclChangeset::STATUS_FINISHED);
            $this->em->persist($changeset);
            $this->em->flush($changeset);

            $hasPending = $aclRepo->hasPendingChangesets();
        } while ($hasPending);
    }

    /**
     * @return boolean
     */
    private function isRunning()
    {
        // Check if we have records in running state, if so read PID & check if process is active
        /* @var AclChangeset $runningAclChangeset */
        $runningAclChangeset = $this->em->getRepository('KunstmaanAdminBundle:AclChangeset')->findRunningChangeset();
        if (!is_null($runningAclChangeset)) {
            // Found running process, check if PID is still running
            if (!$this->shellHelper->isRunning($runningAclChangeset->getPid())) {
                // PID not running, process probably failed...
                $runningAclChangeset->setStatus(AclChangeset::STATUS_FAILED);
                $this->em->persist($runningAclChangeset);
                $this->em->flush($runningAclChangeset);
            }
        }

        return false;
    }

}
