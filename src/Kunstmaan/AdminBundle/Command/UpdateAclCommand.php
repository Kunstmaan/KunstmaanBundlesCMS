<?php

namespace Kunstmaan\AdminBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Security\Acl\Domain\Acl;
use Symfony\Component\Security\Acl\Domain\Entry;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;

/**
 * Permissions update of ACL entries for all nodes for given role.
 */
class UpdateAclCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('kuma:acl:update')
            ->setDescription('Permissions update of ACL entries for all nodes for given role')
            ->setHelp("The <info>kuma:update:acl</info> will update ACL entries for the nodes of the current project" .
                "with given role and permissions");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        // Select Role
        $roles = $this->getContainer()->getParameter('security.role_hierarchy.roles');
        $question = new ChoiceQuestion('Select role', array_keys($roles));
        $question->setErrorMessage('Role %s is invalid.');
        $role = $helper->ask($input, $output, $question);

        // Select Permission(s)
        $permissionMap = $this->getContainer()->get('security.acl.permission.map');
        $question = new ChoiceQuestion('Select permissions(s) (seperate by ",")',
            $permissionMap->getPossiblePermissions());
        $question->setMultiselect(true);
        $mask = array_reduce($helper->ask($input, $output, $question), function ($a, $b) use ($permissionMap) {
            return $a | $permissionMap->getMasks($b, null)[0];
        }, 0);

        /* @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /* @var MutableAclProviderInterface $aclProvider */
        $aclProvider = $this->getContainer()->get('security.acl.provider');
        /* @var ObjectIdentityRetrievalStrategyInterface $oidStrategy */
        $oidStrategy = $this->getContainer()->get('security.acl.object_identity_retrieval_strategy');

        // Fetch all nodes & grant access
        $nodes = $em->getRepository('KunstmaanNodeBundle:Node')->findAll();

        foreach ($nodes as $node) {
            $objectIdentity = $oidStrategy->getObjectIdentity($node);

            /** @var Acl $acl */
            $acl = $aclProvider->findAcl($objectIdentity);
            $securityIdentity = new RoleSecurityIdentity($role);

            /** @var Entry $ace */
            foreach ($acl->getObjectAces() as $index => $ace) {
                if (!$ace->getSecurityIdentity()->equals($securityIdentity)) {
                    continue;
                }
                $acl->updateObjectAce($index, $mask);
                break;
            }
            $aclProvider->updateAcl($acl);
        }
        $output->writeln(count($nodes) . ' nodes processed.');
    }

}
