<?php

namespace Kunstmaan\AdminBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\AclChangeset;
use Kunstmaan\AdminBundle\Service\AclManager;
use Kunstmaan\UtilitiesBundle\Helper\Shell\Shell;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Symfony CLI command to apply the {@link AclChangeSet} with status {@link AclChangeSet::STATUS_NEW} to their entities
 */
final class ApplyAclCommand extends Command
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Shell */
    private $shellHelper;

    /** @var AclManager */
    private $aclManager;

    public function __construct(AclManager $aclManager, EntityManagerInterface $em, Shell $shellHelper)
    {
        parent::__construct();

        $this->aclManager = $aclManager;
        $this->em = $em;
        $this->shellHelper = $shellHelper;
    }

    protected function configure(): void
    {
        parent::configure();

        $this->setName('kuma:acl:apply')
             ->setDescription('Apply ACL changeset.')
             ->setHelp('The <info>kuma:acl:apply</info> can be used to apply an ACL changeset recursively, changesets are fetched from the database.');
    }

    /**
     * Apply the {@link AclChangeSet} with status {@link AclChangeSet::STATUS_NEW} to their entities
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Check if another ACL apply process is currently running & do nothing if it is
        if ($this->isRunning()) {
            return 0;
        }

        $this->aclManager->applyAclChangesets();

        return 0;
    }

    private function isRunning(): bool
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
