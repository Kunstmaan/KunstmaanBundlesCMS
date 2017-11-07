<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\Exception;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();
        $exception = $event->getException();

        if ( $exception instanceof HttpExceptionInterface ) {
            $model = new Exception;
            $model->setCode($exception->getStatusCode());
            $model->setUrl($request->getUri());
            $model->setUrlReferer($request->headers->get('referer'));

            $this->em->persist($model);
            $this->em->flush();
        }
    }
}