<?php

namespace Kunstmaan\AdminBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\Role;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Symfony CLI command to create a group using bin/console kuma:role:create <NAME_OF_THE_ROLE>
 */
final class CreateRoleCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function configure()
    {
        $this->setName('kuma:role:create')
            ->setDescription('Create a role.')
            ->setDefinition([
                new InputArgument('role', InputArgument::REQUIRED, 'The role'),
            ])
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
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $roleName = strtoupper($input->getArgument('role'));
        if ('ROLE_' != substr($roleName, 0, 5)) {
            $roleName = 'ROLE_' . $roleName;
        }

        $role = new Role($roleName);
        $this->em->persist($role);
        $this->em->flush();

        $output->writeln(sprintf('Created role <comment>%s</comment>', $roleName));

        return 0;
    }
}
