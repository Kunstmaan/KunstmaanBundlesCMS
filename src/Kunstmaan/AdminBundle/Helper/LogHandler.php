<?php

namespace Kunstmaan\AdminBundle\Helper;

use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Entity\ErrorLogItem;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\EntityManager;

use Monolog\Logger;
use Monolog\Handler\NullHandler;
use Monolog\Handler\AbstractProcessingHandler;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * LogHandler
 */
class LogHandler extends AbstractProcessingHandler
{
    /**
     * @var ContainerInterface $container
     */
    private $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container The container
     * @param int                $level     The log level
     * @param bool               $bubble    Bubble or not
     */
    public function __construct(ContainerInterface $container, $level = Logger::ERROR, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->container = $container;
    }

    /**
     * Write a record using the monolog logger service.
     *
     * @param array $record
     */
    protected function write(array $record)
    {
        $logger = $this->container->get('monolog.logger.doctrine');
        $logger->pushHandler(new NullHandler());
        try {
            /* @var TokenInterface $token */
            $token = $this->container->get('security.context')->getToken();
            $user  = null;
            if (isset($token)) {
                /* @var User $user */
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
            /* @var EntityManager $em */
            $em = $this->container->get('doctrine')->getEntityManager();
            if ($em->isOpen()) {
                $em->persist($logItem);
                $em->flush();
            }
        } catch (\PDOException $e) {
            // catching the exception during fullreload: errorlogitem table not found
            // TODO do something useful
        } catch (\ORMException $e) {
            // catching the exception during fullreload: The EntityManager is closed
            // TODO do something useful
        }
        $logger->popHandler();
    }
}
