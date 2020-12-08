<?php

namespace Kunstmaan\AdminBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\AclChangeset;
use Kunstmaan\AdminBundle\Service\AclManager;
use Kunstmaan\UtilitiesBundle\Helper\Shell\Shell;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Symfony CLI command to apply the {@link AclChangeSet} with status {@link AclChangeSet::STATUS_NEW} to their entities
 *
 * @final since 5.1
 * NEXT_MAJOR extend from `Command` and remove `$this->getContainer` usages
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

    /** @var AclManager */
    private $aclManager = null;

    public function __construct(/*AclManager*/ $aclManager = null, EntityManagerInterface $em = null, Shell $shellHelper = null)
    {
        parent::__construct();

        if (!$aclManager instanceof AclManager) {
            @trigger_error(sprintf('Passing a command name as the first argument of "%s" is deprecated since version symfony 3.4 and will be removed in symfony 4.0. If the command was registered by convention, make it a service instead. ', __METHOD__), E_USER_DEPRECATED);

            $this->setName(null === $aclManager ? 'kuma:acl:apply' : $aclManager);

            return;
        }

        $this->aclManager = $aclManager;
        $this->em = $em;
        $this->shellHelper = $shellHelper;
    }

    protected function configure()
    {
        parent::configure();

        $this->setName('kuma:acl:apply')
             ->setDescription('Apply ACL changeset.')
             ->setHelp('The <info>kuma:acl:apply</info> can be used to apply an ACL changeset recursively, changesets are fetched from the database.');
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
        if (null === $this->aclManager) {
            $this->aclManager = $this->getContainer()->get('kunstmaan_admin.acl.manager');
        }
        if (null === $this->em) {
            $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        }
        if (null === $this->shellHelper) {
            $this->shellHelper = $this->getContainer()->get('kunstmaan_utilities.shell');
        }

        // Check if another ACL apply process is currently running & do nothing if it is
        if ($this->isRunning()) {
            return 0;
        }

        $this->aclManager->applyAclChangesets();

        return 0;
    }

    /**
     * @return bool
     */
    private function isRunning()
    {
        // Check if we have records in running state, if so read PID & check if process is active
        /* @var AclChangeset $runningAclChangeset */
        $runningAclChangeset = $this->em->getRepository(AclChangeset::class)->findRunningChangeset();
        // Found running process, check if PID is still running
        if (!\is_null($runningAclChangeset) && !$this->shellHelper->isRunning($runningAclChangeset->getPid())) {
            // PID not running, process probably failed...
            $runningAclChangeset->setStatus(AclChangeset::STATUS_FAILED);
            $this->em->persist($runningAclChangeset);
            $this->em->flush();
        }

        return false;
    }
}
