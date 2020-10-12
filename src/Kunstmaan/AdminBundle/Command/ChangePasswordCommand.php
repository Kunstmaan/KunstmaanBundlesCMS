<?php

namespace Kunstmaan\AdminBundle\Command;

use FOS\UserBundle\Model\UserManager as FOSUserManager;
use InvalidArgumentException;
use Kunstmaan\AdminBundle\Service\UserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

final class ChangePasswordCommand extends Command
{
    protected static $defaultName = 'kuma:user:change-password';

    /** @var UserManager */
    private $userManager;

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
            ->setName('kuma:user:change-password')
            ->setDescription('Change the password of a user.')
            ->setDefinition([
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
            ])
            ->setHelp(<<<'EOT'
The <info>kuma:user:change-password</info> command changes the password of a user:

  <info>php %command.full_name% matthieu</info>

This interactive shell will first ask you for a password.

You can alternatively specify the password as a second argument:

  <info>php %command.full_name% matthieu mypassword</info>

EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $user = $this->userManager->findUserByUsername($username);

        if (!$user) {
            throw new InvalidArgumentException(sprintf('User identified by "%s" username does not exist.', $username));
        }

        $user->setPlainPassword($password);
        $this->userManager->updateUser($user);

        $output->writeln(sprintf('Changed password for user <comment>%s</comment>', $username));
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questions = [];

        if (!$input->getArgument('username')) {
            $question = new Question('Please give the username:');
            $question->setValidator(function ($username) {
                if (empty($username)) {
                    throw new InvalidArgumentException('Username can not be empty');
                }

                return $username;
            });
            $questions['username'] = $question;
        }

        if (!$input->getArgument('password')) {
            $question = new Question('Please enter the new password:');
            $question->setValidator(function ($password) {
                if (empty($password)) {
                    throw new InvalidArgumentException('Password can not be empty');
                }

                return $password;
            });
            $question->setHidden(true);
            $questions['password'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }
}
