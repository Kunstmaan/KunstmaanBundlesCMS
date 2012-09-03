<?php

/*
 * Copyright (c) 2012 Kunstmaan (http://www.kunstmaan.be)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Wim Vandersmissen <wim.vandersmissen@kunstmaan.be>
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace Kunstmaan\AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\ORM\EntityManager;

use Kunstmaan\AdminBundle\Entity\User;

class CreateUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('kuma:user:create')
            ->setDescription('Create a user.')
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                new InputOption('group', null, InputOption::VALUE_REQUIRED, 'The group(s) the user should belong to'),
                new InputOption('super-admin', null, InputOption::VALUE_NONE, 'Set the user as super admin'),
                new InputOption('inactive', null, InputOption::VALUE_NONE, 'Set the user as inactive'),
            ))
            ->setHelp(<<<EOT
The <info>kuma:user:create</info> command creates a user:

  <info>php app/console kuma:user:create matthieu --group=Users</info>

This interactive shell will ask you for an email and then a password.

You can alternatively specify the email and password as the second and third arguments:

  <info>php app/console kuma:user:create matthieu matthieu@example.com mypassword --group=Users</info>

You can create a super admin via the super-admin flag:

  <info>php app/console kuma:user:create admin --super-admin --group=Administrators</info>

You can create an inactive user (will not be able to log in):

  <info>php app/console kuma:user:create thibault --inactive --group=Users</info>

<comment>Note:</comment> You have to specify at least one group.

EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $username = $input->getArgument('username');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $superAdmin = $input->getOption('super-admin');
        $inactive = $input->getOption('inactive');
        $groupOption = $input->getOption('group');

        $command = $this->getApplication()->find('fos:user:create');
        $arguments = array(
            'command'       => 'fos:user:create',
            'username'      => $username,
            'email'         => $email,
            'password'      => $password,
            '--super-admin' => $superAdmin,
            '--inactive'    => $inactive,
        );

        $input = new ArrayInput($arguments);
        $command->run($input, $output);

        // Fetch user that was just created
        /* @var User $user */
        $user = $em->getRepository('KunstmaanAdminBundle:User')->findOneBy(array('username' => $username));

        // Attach groups
        $groupNames = explode(',', $groupOption);
        foreach ($groupNames as $groupName) {
            $group = $em->getRepository('KunstmaanAdminBundle:Group')->findOneBy(array('name' => $groupName));
            $user->getGroups()->add($group);
        }
        // Persist
        $em->persist($user);
        $em->flush();

        $output->writeln(sprintf('Added user <comment>%s</comment> to groups <comment>%s</comment>',
                $input->getArgument('username'),
                $groupOption));
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('username')) {
            $username = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a username:',
                function($username) {
                    if (empty($username)) {
                        throw new \Exception('Username can not be empty');
                    }

                    return $username;
                }
            );
            $input->setArgument('username', $username);
        }

        if (!$input->getArgument('email')) {
            $email = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose an email:',
                function($email) {
                    if (empty($email)) {
                        throw new \Exception('Email can not be empty');
                    }

                    return $email;
                }
            );
            $input->setArgument('email', $email);
        }

        if (!$input->getArgument('password')) {
            $password = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a password:',
                function($password) {
                    if (empty($password)) {
                        throw new \Exception('Password can not be empty');
                    }

                    return $password;
                }
            );
            $input->setArgument('password', $password);
        }

        if (!$input->getOption('group')) {
            $group = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please enter the group(s) the user should be a member of:',
                function($group) {
                    if (empty($group)) {
                        throw new \Exception('Groups can not be empty');
                    }

                    return $group;
                }
            );
            $input->setOption('group', $group);
        }
    }
}
