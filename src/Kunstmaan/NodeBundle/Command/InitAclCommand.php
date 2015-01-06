<?php

namespace Kunstmaan\NodeBundle\Command;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;

/**
 * Basic initialization of ACL entries for all nodes.
 */
class InitAclCommand extends ContainerAwareCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('kuma:init:acl')
            ->setDescription('Basic initialization of ACL for projects')
            ->setHelp("The <info>kuma:init:acl</info> will create basic ACL entries for the nodes of the current project");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /* @var MutableAclProviderInterface $aclProvider */
        $aclProvider = $this->getContainer()->get('security.acl.provider');
        /* @var ObjectIdentityRetrievalStrategyInterface $oidStrategy */
        $oidStrategy = $this->getContainer()->get('security.acl.object_identity_retrieval_strategy');

        // Fetch all nodes & grant access
        $nodes = $em->getRepository('KunstmaanNodeBundle:Node')->findAll();
        $count = 0;
        foreach ($nodes as $node) {
            $count++;
            $objectIdentity = $oidStrategy->getObjectIdentity($node);
            try {
                $aclProvider->deleteAcl($objectIdentity);
            } catch (AclNotFoundException $e) {
                // Do nothing
            }
            $acl = $aclProvider->createAcl($objectIdentity);

            $securityIdentity = new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY');
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_VIEW);

            $securityIdentity = new RoleSecurityIdentity('ROLE_ADMIN');
            $acl->insertObjectAce(
                $securityIdentity,
                MaskBuilder::MASK_VIEW | MaskBuilder::MASK_EDIT | MaskBuilder::MASK_PUBLISH | MaskBuilder::MASK_UNPUBLISH
            );

            $securityIdentity = new RoleSecurityIdentity('ROLE_SUPER_ADMIN');
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_IDDQD);
            $aclProvider->updateAcl($acl);
        }
        $output->writeln("{$count} nodes processed.");
    }

}
