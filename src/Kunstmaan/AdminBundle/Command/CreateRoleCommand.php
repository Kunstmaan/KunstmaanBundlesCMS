<?php

namespace Kunstmaan\AdminBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\Role;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'kuma:role:create', description: 'Create a role.')]
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

    protected function configure(): void
    {
        $this
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

    protected function execute(InputInterface $input, OutputInterface $output): int
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
