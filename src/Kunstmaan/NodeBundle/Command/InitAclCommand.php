<?php

namespace Kunstmaan\NodeBundle\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;

/**
 * Basic initialization of ACL entries for all nodes.
 *
 * @final since 5.1
 * NEXT_MAJOR extend from `Command` and remove `$this->getContainer` usages
 */
class InitAclCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager
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

    /**
     * @param EntityManagerInterface|null                   $em
     * @param MutableAclProviderInterface|null              $aclProvider
     * @param ObjectIdentityRetrievalStrategyInterface|null $oidStrategy
     */
    public function __construct(/* EntityManagerInterface */ $em = null, /* MutableAclProviderInterface */ $aclProvider = null, /* ObjectIdentityRetrievalStrategyInterface */ $oidStrategy = null)
    {
        parent::__construct();

        if (!$em instanceof EntityManagerInterface) {
            @trigger_error(sprintf('Passing a command name as the first argument of "%s" is deprecated since version symfony 3.4 and will be removed in symfony 4.0. If the command was registered by convention, make it a service instead. ', __METHOD__), E_USER_DEPRECATED);

            $this->setName(null === $em ? 'kuma:init:acl' : $em);

            return;
        }

        $this->em = $em;
        $this->aclProvider = $aclProvider;
        $this->oidStrategy = $oidStrategy;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('kuma:init:acl')
            ->setDescription('Basic initialization of ACL for projects')
            ->setHelp('The <info>kuma:init:acl</info> will create basic ACL entries for the nodes of the current project');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null === $this->em) {
            $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
            $this->aclProvider = $this->getContainer()->get('security.acl.provider');
            $this->oidStrategy = $this->getContainer()->get('security.acl.object_identity_retrieval_strategy');
        }

        // Fetch all nodes & grant access
        $nodes = $this->em->getRepository('KunstmaanNodeBundle:Node')->findAll();
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
    }
}
