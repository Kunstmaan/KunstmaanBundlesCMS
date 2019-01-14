<?php

namespace Kunstmaan\AdminBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Service\AclManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Security\Acl\Domain\Acl;
use Symfony\Component\Security\Acl\Permission\PermissionMapInterface;

/**
 * Permissions update of ACL entries for all nodes for given role.
 *
 * @final since 5.1
 * NEXT_MAJOR extend from `Command` and remove `$this->getContainer` usages
 */
class UpdateAclCommand extends ContainerAwareCommand
{
    /** @var AclManager */
    private $aclManager;

    /** @var PermissionMapInterface */
    private $permissionMap;

    /** @var EntityManagerInterface */
    private $em;

    /** @var  */
    private $roles;

    public function __construct(/*AclManager*/ $aclManager = null, EntityManagerInterface $em = null, PermissionMapInterface $permissionMap = null, array $roles = null)
    {
        parent::__construct();

        if (!$aclManager instanceof AclManager) {
            @trigger_error(sprintf('Passing a command name as the first argument of "%s" is deprecated since version symfony 3.4 and will be removed in symfony 4.0. If the command was registered by convention, make it a service instead. ', __METHOD__), E_USER_DEPRECATED);

            $this->setName(null === $aclManager ? 'kuma:acl:update' : $aclManager);

            return;
        }

        $this->aclManager = $aclManager;
        $this->em = $em;
        $this->permissionMap = $permissionMap;
        $this->roles = $roles;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('kuma:acl:update')
            ->setDescription('Permissions update of ACL entries for all nodes for given role')
            ->setHelp('The <info>kuma:acl:update</info> will update ACL entries for the nodes of the current project' .
                'with given role and permissions');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        if (null === $this->aclManager) {
            $this->aclManager = $this->getContainer()->get('kunstmaan_admin.acl.manager');
        }
        if (null === $this->em) {
            $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        }
        if (null === $this->permissionMap) {
            $this->permissionMap = $this->getContainer()->get('security.acl.permission.map');
        }
        if (null === $this->roles) {
            $this->roles = $this->getContainer()->getParameter('security.role_hierarchy.roles');
        }

        // Select Role
        $question = new ChoiceQuestion('Select role', array_keys($this->roles));
        $question->setErrorMessage('Role %s is invalid.');
        $role = $helper->ask($input, $output, $question);

        // Select Permission(s)
        $permissionMap = $this->permissionMap;
        $question = new ChoiceQuestion('Select permissions(s) (separate by ",")',
            $permissionMap->getPossiblePermissions());
        $question->setMultiselect(true);
        $mask = array_reduce($helper->ask($input, $output, $question), function ($a, $b) use ($permissionMap) {
            return $a | $permissionMap->getMasks($b, null)[0];
        }, 0);

        // Fetch all nodes & grant access
        $nodes = $this->em->getRepository('KunstmaanNodeBundle:Node')->findAll();

        $this->aclManager->updateNodesAclToRole($nodes, $role, $mask);

        $output->writeln(count($nodes) . ' nodes processed.');
    }
}
