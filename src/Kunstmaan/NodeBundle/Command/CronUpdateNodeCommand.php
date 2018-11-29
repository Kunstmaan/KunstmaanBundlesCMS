<?php

namespace Kunstmaan\NodeBundle\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\NodeBundle\Entity\QueuedNodeTranslationAction;
use Kunstmaan\NodeBundle\Helper\NodeAdmin\NodeAdminPublisher;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @final since 5.1
 * NEXT_MAJOR extend from `Command` and remove `$this->getContainer` usages
 */
class CronUpdateNodeCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var NodeAdminPublisher
     */
    private $nodePublisher;

    /**
     * @param EntityManagerInterface|null $em
     * @param TokenStorage|null           $tokenStorage
     * @param NodeAdminPublisher|null     $nodePublisher
     */
    public function __construct(/* EntityManagerInterface */ $em = null, /* TokenStorage */$tokenStorage = null, /* NodeAdminPublisher */ $nodePublisher = null)
    {
        parent::__construct();

        if (!$em instanceof EntityManagerInterface) {
            @trigger_error(sprintf('Passing a command name as the first argument of "%s" is deprecated since version symfony 3.4 and will be removed in symfony 4.0. If the command was registered by convention, make it a service instead. ', __METHOD__), E_USER_DEPRECATED);

            $this->setName(null === $em ? 'kuma:nodes:cron' : $em);

            return;
        }

        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->nodePublisher = $nodePublisher;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('kuma:nodes:cron')
            ->setDescription('Do everything that needs to be run in a cron job.')
            ->setHelp('The <info>kuma:nodes:cron</info> will loop over all queued node translation action entries and update the nodetranslations if needed.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null === $this->em) {
            $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
            $this->tokenStorage = $this->getContainer()->get('security.token_storage');
            $this->nodePublisher = $this->getContainer()->get('kunstmaan_node.admin_node.publisher');
        }

        $queuedNodeTranslationActions = $this->em->getRepository('KunstmaanNodeBundle:QueuedNodeTranslationAction')->findAll();

        if (count($queuedNodeTranslationActions)) {
            foreach ($queuedNodeTranslationActions as $queuedNodeTranslationAction) {
                $now = new \DateTime();
                if ($queuedNodeTranslationAction->getDate()->getTimestamp() < $now->getTimestamp()) {
                    $action = $queuedNodeTranslationAction->getAction();
                    {
                        // Set user security context
                        $user = $queuedNodeTranslationAction->getUser();
                        $runAsToken = new UsernamePasswordToken($user, null, 'foo', $user->getRoles());
                        $this->tokenStorage->setToken($runAsToken);
                    }
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
    }
}
