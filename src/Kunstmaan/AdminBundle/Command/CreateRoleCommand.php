<?php

namespace Kunstmaan\AdminBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Symfony CLI command to create a group using bin/console kuma:role:create <NAME_OF_THE_ROLE>
 *
 * @final since 5.1
 * NEXT_MAJOR extend from `Command` and remove `$this->getContainer` usages
 */
class CreateRoleCommand extends ContainerAwareCommand
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

            $this->setName(null === $em ? 'kuma:role:create' : $em);

            return;
        }

        $this->em = $em;
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('kuma:role:create')
            ->setDescription('Create a role.')
            ->setDefinition(array(
                new InputArgument('role', InputArgument::REQUIRED, 'The role'),
            ))
            ->setHelp(<<<'EOT'
The <info>kuma:role:create</info> command creates a role:

  <info>php bin/console kuma:role:create ROLE_ADMIN</info>

<comment>Note:</comment> The ROLE_ prefix will be added if you don't provide it

  <info>php bin/console kuma:role:create ADMIN</info>

will create ROLE_ADMIN.

EOT
            );
    }

    /**
     * Executes the current command.
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

        $roleName = strtoupper($input->getArgument('role'));
        if ('ROLE_' != substr($roleName, 0, 5)) {
            $roleName = 'ROLE_' . $roleName;
        }

        $role = new Role($roleName);
        $this->em->persist($role);
        $this->em->flush();

        $output->writeln(sprintf('Created role <comment>%s</comment>', $roleName));
    }
}
