<?php

namespace Kunstmaan\NodeBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Kunstmaan\NodeBundle\Entity\Node;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;

/**
 * Basic initialization of ACL entries for all nodes.
 */
#[AsCommand(name: 'kuma:init:acl', description: 'Basic initialization of ACL for projects')]
final class InitAclCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var MutableAclProviderInterface
     */
    private $aclProvider;

    /**
     * @var ObjectIdentityRetrievalStrategyInterface
     */
    private $oidStrategy;

    public function __construct(EntityManagerInterface $em, MutableAclProviderInterface $aclProvider, ObjectIdentityRetrievalStrategyInterface $oidStrategy)
    {
        parent::__construct();

        $this->em = $em;
        $this->aclProvider = $aclProvider;
        $this->oidStrategy = $oidStrategy;
    }

    protected function configure(): void
    {
        $this
            ->setHelp('The <info>kuma:init:acl</info> will create basic ACL entries for the nodes of the current project')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Fetch all nodes & grant access
        $nodes = $this->em->getRepository(Node::class)->findAll();
        $count = 0;
        foreach ($nodes as $node) {
            ++$count;
            $objectIdentity = $this->oidStrategy->getObjectIdentity($node);

            try {
                $this->aclProvider->deleteAcl($objectIdentity);
            } catch (AclNotFoundException $e) {
                // Do nothing
            }
            $acl = $this->aclProvider->createAcl($objectIdentity);

            $securityIdentity = new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY');
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_VIEW);

            if (defined(AuthenticatedVoter::PUBLIC_ACCESS)) {
                $securityIdentity = new RoleSecurityIdentity(AuthenticatedVoter::PUBLIC_ACCESS);
                $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_VIEW);
            }

            $securityIdentity = new RoleSecurityIdentity('ROLE_ADMIN');
            $acl->insertObjectAce(
                $securityIdentity,
                MaskBuilder::MASK_VIEW | MaskBuilder::MASK_EDIT | MaskBuilder::MASK_PUBLISH | MaskBuilder::MASK_UNPUBLISH
            );

            $securityIdentity = new RoleSecurityIdentity('ROLE_SUPER_ADMIN');
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_IDDQD);
            $this->aclProvider->updateAcl($acl);
        }
        $output->writeln("{$count} nodes processed.");

        return 0;
    }
}
