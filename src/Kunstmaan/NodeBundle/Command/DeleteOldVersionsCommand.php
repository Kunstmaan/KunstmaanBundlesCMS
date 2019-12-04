<?php

namespace Kunstmaan\NodeBundle\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Entity\QueuedNodeTranslationAction;
use Kunstmaan\NodeBundle\Helper\NodeAdmin\NodeAdminPublisher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @final since 5.1
 */
class DeleteOldVersionsCommand extends Command
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(/* EntityManagerInterface */ $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function configure()
    {
        parent::configure();

        $this->setName('kuma:nodes:clean-old-versions')
            ->setDescription('Clean up old node versions that will not be used anymore.')
            ->setHelp('The <info>kuma:nodes:clean-old-versions</info> will delete old node versions.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em->getRepository(NodeVersion::class)->removeOldNodeVersions();
        $this->em->flush();
    }
}
