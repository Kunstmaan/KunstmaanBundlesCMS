<?php

namespace Kunstmaan\AdminBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;

final class PromoteUserCommand extends RoleCommand
{
    protected static $defaultName = 'kuma:user:promote';

    protected function configure()
    {
        parent::configure();

        $this
            ->setDescription('Promotes a user by adding a role')
            ->setHelp(<<<'EOT'
The <info>kuma:user:promote</info> command promotes a user by adding a role

  <info>php %command.full_name% matthieu ROLE_CUSTOM</info>
  <info>php %command.full_name% --super matthieu</info>
EOT
            );
    }

    protected function executeRoleCommand(OutputInterface $output, string $username, bool $super, string $role): int
    {
        $user = $this->userManager->findUserByUsername($username);

        if (!$user) {
            throw new InvalidArgumentException(sprintf('User identified by "%s" username does not exist.', $username));
        }

        if ($super) {
            $user->setSuperAdmin(true);
            $output->writeln(sprintf('User "%s" has been promoted as a super administrator. This change will not apply until the user logs out and back in again.', $username));
        } else {
            if ($user->hasRole($role)) {
                $output->writeln(sprintf('User "%s" did already have "%s" role.', $username, $role));

                return 1;
            }

            $user->addRole($role);
            $output->writeln(sprintf('Role "%s" has been added to user "%s". This change will not apply until the user logs out and back in again.', $role, $username));
        }

        $this->userManager->updateUser($user);

        return 0;
    }
}
