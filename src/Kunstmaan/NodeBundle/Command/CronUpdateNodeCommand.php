<?php

namespace Kunstmaan\NodeBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\NodeBundle\Entity\QueuedNodeTranslationAction;
use Kunstmaan\NodeBundle\Helper\NodeAdmin\NodeAdminPublisher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

#[AsCommand(name: 'kuma:nodes:cron', description: 'Do everything that needs to be run in a cron job.')]
final class CronUpdateNodeCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var NodeAdminPublisher
     */
    private $nodePublisher;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, NodeAdminPublisher $nodePublisher)
    {
        parent::__construct();

        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->nodePublisher = $nodePublisher;
    }

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setHelp('The <info>kuma:nodes:cron</info> will loop over all queued node translation action entries and update the nodetranslations if needed.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $queuedNodeTranslationActions = $this->em->getRepository(QueuedNodeTranslationAction::class)->findAll();

        if (\count($queuedNodeTranslationActions)) {
            foreach ($queuedNodeTranslationActions as $queuedNodeTranslationAction) {
                $now = new \DateTime();
                if ($queuedNodeTranslationAction->getDate()->getTimestamp() < $now->getTimestamp()) {
                    $action = $queuedNodeTranslationAction->getAction();

                    // Set user security context
                    $user = $queuedNodeTranslationAction->getUser();
                    $runAsToken = new UsernamePasswordToken($user, null, 'foo', $user->getRoles());
                    $this->tokenStorage->setToken($runAsToken);

                    $nodeTranslation = $queuedNodeTranslationAction->getNodeTranslation();
                    switch ($action) {
                        case QueuedNodeTranslationAction::ACTION_PUBLISH:
                            $this->nodePublisher->publish($nodeTranslation, $user);
                            $output->writeln('Published the page ' . $nodeTranslation->getTitle());

                            break;
                        case QueuedNodeTranslationAction::ACTION_UNPUBLISH:
                            $this->nodePublisher->unPublish($nodeTranslation);
                            $output->writeln('Unpublished the page ' . $nodeTranslation->getTitle());

                            break;
                        default:
                            $output->writeln("Don't understand the action " . $action);
                    }
                }
            }
            $output->writeln('Done');
        } else {
            $output->writeln('No queued jobs');
        }

        return 0;
    }
}
