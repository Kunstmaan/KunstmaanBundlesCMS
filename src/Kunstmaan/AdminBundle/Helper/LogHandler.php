<?php

namespace Kunstmaan\AdminBundle\Helper;

use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Entity\ErrorLogItem;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\EntityManager;

use Monolog\Logger;
use Monolog\Handler\NullHandler;
use Monolog\Handler\AbstractProcessingHandler;

use Symfony\Bridge\Monolog\Logger as MonologLogger;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\SecurityContextInterface;

class LogHandler extends AbstractProcessingHandler
{
    private $initialized = false;
    private $logger;
    private $securityContext;
    private $em;

    public function __construct(
        MonologLogger $logger,
        SecurityContextInterface $context,
        EntityManager $em,
        $level = Logger::ERROR,
        $bubble = true
    ) {
        parent::__construct($level, $bubble);
        $this->logger          = $logger;
        $this->securityContext = $context;
        $this->em              = $em;
    }

    protected function write(array $record)
    {
        if (!$this->initialized) {
            $this->initialize();
        }
        $this->logger->pushHandler(new NullHandler());
        try {
            $token = $this->securityContext->getToken();
            $user  = null;
            if (isset($token)) {
                $user = $token->getUser();
            }

            $logItem = new ErrorLogItem();
            if ($user instanceof User) {
                $logItem->setUser($user);
            }
            $logItem->setStatus("error");
            $logItem->setChannel($record['channel']);
            $logItem->setLevel($record['level']);
            $logItem->setMessage($record['formatted']);
            if ($this->em->isOpen()) {
                $this->em->persist($logItem);
                $this->em->flush();
            }
        } catch (\PDOException $e) {
            // catching the exception during fullreload: errorlogitem table not found
            // TODO do something useful
        } catch (\ORMException $e) {
            // catching the exception during fullreload: The EntityManager is closed
            // TODO do something useful
        }
        $this->logger->popHandler();
    }

    private function initialize()
    {
        $this->initialized = true;
    }
}