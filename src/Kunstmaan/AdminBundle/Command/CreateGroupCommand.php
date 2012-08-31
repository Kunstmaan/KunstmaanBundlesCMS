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
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\ORM\EntityManager;

use Kunstmaan\AdminBundle\Entity\Group;

class CreateGroupCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('kuma:group:create')
            ->setDescription('Create a user group.')
            ->setDefinition(array(
                new InputArgument('group', InputArgument::REQUIRED, 'The group'),
                new InputOption('role', null, InputOption::VALUE_OPTIONAL, 'Role(s) (comma separated list if you want to specifiy multiple roles)'),
            ))
            ->setHelp(<<<EOT
The <info>kuma:group:create</info> command creates a group:

  <info>php app/console kuma:group:create Administrators</info>

You can specify a list of roles to attach to this group by specifying the
optional --roles parameter, providing a comma separated list of roles :

  <info>php app/console kuma:group:create --role=admin,guest Administrators</info>

<comment>Note:</comment> The ROLE_ prefix will be added if you don't provide it AND you must make
sure the roles already exist!

EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var EntityManager */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $groupname = $input->getArgument('group');
        $rolenames = $input->getOption('role');

        $group = new Group($groupname);

        if (!empty($rolenames)) {
            // Roles were provided, so attach them to the group
            $rolenames = explode(',', strtoupper($rolenames));
            foreach ($rolenames as $rolename) {
                if ('ROLE_' != substr($rolename, 0, 5)) {
                    $rolename = 'ROLE_' . $rolename;
                }
                $role = $em->getRepository('KunstmaanAdminBundle:Role')->findOneBy(array('role' => $rolename));
                $group->addRole($role);
            }
        }
        $em->persist($group);
        $em->flush();

        $output->writeln(sprintf('Created group <comment>%s</comment>', $groupname));
    }
}
