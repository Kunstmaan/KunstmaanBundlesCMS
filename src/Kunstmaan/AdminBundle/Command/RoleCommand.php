<?php

namespace Kunstmaan\AdminBundle\Command;

use FOS\UserBundle\Model\UserManager as FOSUserManager;
use InvalidArgumentException;
use Kunstmaan\AdminBundle\Service\UserManager;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

abstract class RoleCommand extends Command
{
    /** @var FOSUserManager|UserManager */
    protected $userManager;

    public function __construct(/* UserManager */ $userManager)
    {
        parent::__construct();

        if (!$userManager instanceof UserManager && !$userManager instanceof FOSUserManager) {
            throw new InvalidArgumentException(sprintf('The "$userManager" argument must be of type "%s" or type "%s"', UserManager::class, FOSUserManager::class));
        }
        if ($userManager instanceof FOSUserManager) {
            // NEXT_MAJOR set the usermanaged typehint to the kunstmaan usermanager.
            @trigger_error(sprintf('Passing the usermanager from FOSUserBundle as the first argument of "%s" is deprecated since KunstmaanAdminBundle 5.8 and will be removed in KunstmaanAdminBundle 6.0. Use the new Kunstmaan Usermanager %s.', __METHOD__, UserManager::class), E_USER_DEPRECATED);
        }

        $this->userManager = $userManager;
    }

    protected function configure()
    {
        $this
            ->setDefinition([
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('role', InputArgument::OPTIONAL, 'The role'),
                new InputOption('super', null, InputOption::VALUE_NONE, 'Instead specifying role, use this to quickly add the super administrator role'),
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $role = $input->getArgument('role');
        $super = (true === $input->getOption('super'));

        if (null !== $role && $super) {
            throw new InvalidArgumentException('You can pass either the role or the --super option (but not both simultaneously).');
        }

        if (null === $role && !$super) {
            throw new RuntimeException('Not enough arguments.');
        }

        return $this->executeRoleCommand($output, $username, $super, $role);
    }

    abstract protected function executeRoleCommand(OutputInterface $output, string $username, bool $super, string $role): int;

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questions = [];

        if (!$input->getArgument('username')) {
            $question = new Question('Please choose a username:');
            $question->setValidator(function ($username) {
                if (empty($username)) {
                    throw new InvalidArgumentException('Username can not be empty');
                }

                return $username;
            });
            $questions['username'] = $question;
        }

        if ((true !== $input->getOption('super')) && !$input->getArgument('role')) {
            $question = new Question('Please choose a role:');
            $question->setValidator(function ($role) {
                if (empty($role)) {
                    throw new InvalidArgumentException('Role can not be empty');
                }

                return $role;
            });
            $questions['role'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }
}
