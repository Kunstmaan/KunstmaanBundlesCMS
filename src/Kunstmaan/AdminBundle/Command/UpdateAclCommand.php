<?php

namespace Kunstmaan\AdminBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Service\AclManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Security\Acl\Permission\PermissionMapInterface;

/**
 * Permissions update of ACL entries for all nodes for given role.
 */
final class UpdateAclCommand extends Command
{
    /** @var AclManager */
    private $aclManager;

    /** @var PermissionMapInterface */
    private $permissionMap;

    /** @var EntityManagerInterface */
    private $em;

    /** @var array */
    private $roles;

    public function __construct(AclManager $aclManager, EntityManagerInterface $em, PermissionMapInterface $permissionMap, array $roles)
    {
        parent::__construct();

        $this->aclManager = $aclManager;
        $this->em = $em;
        $this->permissionMap = $permissionMap;
        $this->roles = $roles;
    }

    protected function configure()
    {
        parent::configure();

        $this->setName('kuma:acl:update')
            ->setDescription('Permissions update of ACL entries for all nodes for given role')
            ->setHelp('The <info>kuma:acl:update</info> will update ACL entries for the nodes of the current project' .
                'with given role and permissions');
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

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

        $output->writeln(\count($nodes) . ' nodes processed.');

        return 0;
    }
}
