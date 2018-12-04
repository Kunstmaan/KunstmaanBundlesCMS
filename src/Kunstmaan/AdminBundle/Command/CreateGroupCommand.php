<?php

namespace Kunstmaan\AdminBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\Group;
use Kunstmaan\AdminBundle\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Symfony CLI command to create a group using bin/console kuma:group:create <name_of_the_group>
 *
 * @final since 5.1
 * NEXT_MAJOR extend from `Command` and remove `$this->getContainer` usages
 */
class CreateGroupCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface|null $em
     */
    public function __construct(/* EntityManagerInterface */ $em = null)
    {
        parent::__construct();

        if (!$em instanceof EntityManagerInterface) {
            @trigger_error(sprintf('Passing a command name as the first argument of "%s" is deprecated since version symfony 3.4 and will be removed in symfony 4.0. If the command was registered by convention, make it a service instead. ', __METHOD__), E_USER_DEPRECATED);

            $this->setName(null === $em ? 'kuma:group:create' : $em);

            return;
        }

        $this->em = $em;
    }

    /**
     * Configures the current command
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('kuma:group:create')
            ->setDescription('Create a user group.')
            ->setDefinition(array(
                new InputArgument('group', InputArgument::REQUIRED, 'The group'),
                new InputOption('role', null, InputOption::VALUE_OPTIONAL, 'Role(s) (comma separated list if you want to specifiy multiple roles)'),
            ))
            ->setHelp(<<<'EOT'
The <info>kuma:group:create</info> command creates a group:

  <info>php bin/console kuma:group:create Administrators</info>

You can specify a list of roles to attach to this group by specifying the
optional --roles parameter, providing a comma separated list of roles :

  <info>php bin/console kuma:group:create --role=admin,guest Administrators</info>

<comment>Note:</comment> The ROLE_ prefix will be added if you don't provide it AND you must make
sure the roles already exist!

EOT
            );
    }

    /**
     * Executes the current command
     *
     * @param InputInterface  $input  The input
     * @param OutputInterface $output The output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null === $this->em) {
            $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        }

        $groupName = $input->getArgument('group');
        $roleNames = $input->getOption('role');
        $group = new Group($groupName);

        if (!empty($roleNames)) {
            // Roles were provided, so attach them to the group
            $roleNames = explode(',', strtoupper($roleNames));
            foreach ($roleNames as $roleName) {
                if ('ROLE_' != substr($roleName, 0, 5)) {
                    $roleName = 'ROLE_' . $roleName;
                }
                /* @var Role $role */
                $role = $this->em->getRepository('KunstmaanAdminBundle:Role')->findOneBy(array('role' => $roleName));
                $group->addRole($role);
            }
        }
        $this->em->persist($group);
        $this->em->flush();

        $output->writeln(sprintf('Created group <comment>%s</comment>', $groupName));
    }
}
