<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private $em;

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();
        $exception = $event->getException();

        if ( $exception instanceof HttpExceptionInterface ) {

            $hash = md5(
                $exception->getStatusCode() . $request->getUri() . $request->headers->get('referer')
            );

            if ( $model = $this->em->getRepository(Exception::class)->findOneBy(['hash' => $hash]) ) {
                $model->increaseUsed();
                $model->setIsMark(false);

            } else {
                $model = new Exception;
                $model->setCode($exception->getStatusCode());
                $model->setUrl($request->getUri());
                $model->setUrlReferer($request->headers->get('referer'));
                $model->setHash($hash);

            }
            $this->em->persist($model);
            $this->em->flush();
        }
    }
}