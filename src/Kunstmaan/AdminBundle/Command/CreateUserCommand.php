<?php

namespace Kunstmaan\AdminBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\GroupManager as FOSGroupManager;
use FOS\UserBundle\Model\UserManager as FOSUserManager;
use Kunstmaan\AdminBundle\Entity\Group;
use Kunstmaan\AdminBundle\Service\GroupManager;
use Kunstmaan\AdminBundle\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Symfony CLI command to create a user using bin/console kuma:user:create <username_of_the_user>
 *
 * @final since 5.1
 * NEXT_MAJOR extend from `Command` and remove `$this->getContainer` usages
 */
class CreateUserCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'kuma:user:create';

    /** @var array */
    private $groups = [];
    /** @var EntityManagerInterface */
    private $em;
    /** @var GroupManager */
    private $groupManager;
    /** @var UserManager */
    private $userManager;
    /** @var string */
    private $defaultLocale;

    public function __construct(/* EntityManagerInterface */ $em = null, /* GroupManager */ $groupManager = null, /* UserManager */ $userManager = null, $defaultLocale = null)
    {
        parent::__construct();

        if (!$em instanceof EntityManagerInterface) {
            @trigger_error(sprintf('Passing a command name as the first argument of "%s" is deprecated since version symfony 3.4 and will be removed in symfony 4.0. If the command was registered by convention, make it a service instead. ', __METHOD__), E_USER_DEPRECATED);

            $this->setName(null === $em ? 'kuma:user:create' : $em);

            return;
        }

        if (!$groupManager instanceof GroupManager && !$groupManager instanceof FOSGroupManager) {
            throw new \InvalidArgumentException(sprintf('The "$groupManager" argument must be of type "%s" or type "%s"', GroupManager::class, FOSGroupManager::class));
        }
        if ($groupManager instanceof FOSGroupManager) {
            // NEXT_MAJOR set the groupmanager typehint to the kunstmaan groupmanager.
            @trigger_error(sprintf('Passing the groupmanager from FOSUserBundle as the first argument of "%s" is deprecated since KunstmaanAdminBundle 5.9 and will be removed in KunstmaanAdminBundle 6.0. Use the "%s" class instead.', __METHOD__, GroupManager::class), E_USER_DEPRECATED);
        }

        if (!$userManager instanceof UserManager && !$userManager instanceof FOSUserManager) {
            throw new \InvalidArgumentException(sprintf('The "$userManager" argument must be of type "%s" or type "%s"', UserManager::class, FOSUserManager::class));
        }
        if ($userManager instanceof FOSUserManager) {
            // NEXT_MAJOR set the usermanager typehint to the kunstmaan usermanager.
            @trigger_error(sprintf('Passing the usermanager from FOSUserBundle as the first argument of "%s" is deprecated since KunstmaanAdminBundle 5.9 and will be removed in KunstmaanAdminBundle 6.0. Use the "%s" class instead.', __METHOD__, UserManager::class), E_USER_DEPRECATED);
        }

        $this->em = $em;
        $this->groupManager = $groupManager;
        $this->userManager = $userManager;
        $this->defaultLocale = $defaultLocale;
    }

    protected function configure()
    {
        parent::configure();

        $this->setDescription('Create a user.')
            ->setDefinition([
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                new InputArgument('locale', InputArgument::OPTIONAL, 'The locale (language)'),
                new InputOption('group', null, InputOption::VALUE_REQUIRED, 'The group(s) the user should belong to'),
                new InputOption('super-admin', null, InputOption::VALUE_NONE, 'Set the user as super admin'),
                new InputOption('inactive', null, InputOption::VALUE_NONE, 'Set the user as inactive'),
            ])
            ->setHelp(<<<'EOT'
The <info>kuma:user:create</info> command creates a user:

  <info>php bin/console kuma:user:create matthieu --group=Users</info>

This interactive shell will ask you for an email and then a password.

You can alternatively specify the email, password and locale and group as extra arguments:

  <info>php bin/console kuma:user:create matthieu matthieu@example.com mypassword en --group=Users</info>

You can create a super admin via the super-admin flag:

  <info>php bin/console kuma:user:create admin --super-admin --group=Administrators</info>

You can create an inactive user (will not be able to log in):

  <info>php bin/console kuma:user:create thibault --inactive --group=Users</info>

<comment>Note:</comment> You have to specify at least one group.

EOT
            );
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->groups = $this->getGroups();
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null === $this->em) {
            $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
            $this->groupManager = $this->getContainer()->get('kunstmaan_admin.group_manager');
            $this->userManager = $this->getContainer()->get('kunstmaan_admin.user_manager');
            $this->defaultLocale = $this->getContainer()->getParameter('kunstmaan_admin.default_admin_locale');
        }

        $username = $input->getArgument('username');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $locale = $input->getArgument('locale');
        $superAdmin = $input->getOption('super-admin');
        $inactive = $input->getOption('inactive');
        $groupOption = $input->getOption('group');

        if (null === $locale) {
            $locale = $this->defaultLocale;
        }

        $user = $this->userManager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setEnabled(!((bool) $inactive));
        $user->setSuperAdmin((bool) $superAdmin);
        $user->setCreatedBy('kuma:user:create command');
        $this->userManager->updateUser($user);

        $output->writeln(sprintf('Created user <comment>%s</comment>', $username));

        // Attach groups
        $groupOutput = [];

        foreach (explode(',', $groupOption) as $groupId) {
            if ((int) $groupId === 0) {
                foreach ($this->groups as $value) {
                    if ($groupId === $value->getName()) {
                        $group = $value;

                        break;
                    }
                }
            } else {
                $group = $this->groups[$groupId];
            }

            if (isset($group) && $group instanceof Group) {
                $groupOutput[] = $group->getName();
                $user->getGroups()->add($group);
            } else {
                throw new \RuntimeException('The selected group(s) can\'t be found.');
            }
        }

        // Set admin interface locale and enable password changed
        $user->setAdminLocale($locale);
        $user->setPasswordChanged(true);

        // Persist
        $this->userManager->updateUser($user);
        $output->writeln(sprintf('Added user <comment>%s</comment> to groups <comment>%s</comment>', $input->getArgument('username'), implode(',', $groupOutput)));

        return 0;
    }

    /**
     * Interacts with the user.
     *
     * @param InputInterface  $input  The input
     * @param OutputInterface $output The output
     *
     * @throws \InvalidArgumentException
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('username')) {
            $question = new Question('Please choose a username:');
            $question->setValidator(function ($username) {
                if (null === $username) {
                    throw new \InvalidArgumentException('Username can not be empty');
                }

                return $username;
            });
            $username = $this->getHelper('question')->ask(
                $input,
                $output,
                $question
            );
            $input->setArgument('username', $username);
        }

        if (!$input->getArgument('email')) {
            $question = new Question('Please choose an email:');
            $question->setValidator(function ($email) {
                if (null === $email) {
                    throw new \InvalidArgumentException('Email can not be empty');
                }

                return $email;
            });
            $email = $this->getHelper('question')->ask(
                $input,
                $output,
                $question
            );
            $input->setArgument('email', $email);
        }

        if (!$input->getArgument('password')) {
            $question = new Question('Please choose a password:');
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            $question->setValidator(function ($password) {
                if (null === $password) {
                    throw new \InvalidArgumentException('Password can not be empty');
                }

                return $password;
            });
            $password = $this->getHelper('question')->ask(
                $input,
                $output,
                $question
            );

            $input->setArgument('password', $password);
        }

        if (!$input->getArgument('locale')) {
            $locale = $this->getHelper('question')->ask(
                $input,
                $output,
                new Question('Please enter the locale (or leave empty for default admin locale):')
            );
            $input->setArgument('locale', $locale);
        }

        if (!$input->getOption('group')) {
            $question = new ChoiceQuestion(
                'Please enter the group(s) the user should be a member of (multiple possible, separated by comma):',
                $this->groups,
                ''
            );
            $question->setMultiselect(true);
            $question->setValidator(function ($groupsInput) {
                if (!$this->groups) {
                    throw new \RuntimeException('No user group(s) could be found');
                }

                // Validate that the chosen group options exist in the available groups
                $groupNames = array_unique(explode(',', $groupsInput));
                if (\count(array_intersect_key(array_flip($groupNames), $this->groups)) !== \count($groupNames)) {
                    throw new InvalidArgumentException('You have chosen non existing group(s)');
                }

                if ($groupsInput === '') {
                    throw new \RuntimeException('Group(s) must be of type integer and can not be empty');
                }

                return $groupsInput;
            });

            // Group has to be imploded because $input->setOption expects a string
            $groups = $this->getHelper('question')->ask($input, $output, $question);

            $input->setOption('group', $groups);
        }
    }

    private function getGroups()
    {
        $groups = $this->groupManager->findGroups();

        // reindexing the array, using the db id as the key
        $newGroups = [];
        foreach ($groups as $group) {
            $newGroups[$group->getId()] = $group;
        }

        return $newGroups;
    }
}
