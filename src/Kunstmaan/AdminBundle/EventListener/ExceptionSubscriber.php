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
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var array
     */
    private $excludes;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    /**
     * ExceptionSubscriber constructor.
     *
     * @param EntityManagerInterface $em
     * @param array                  $excludes
     */
    public function __construct(EntityManagerInterface $em, array $excludes = [])
    {
        $this->em = $em;
        $this->excludes = $excludes;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();
        $exception = $event->getException();

        if ($exception instanceof HttpExceptionInterface) {
            $uri = $request->getUri();

            if (count($this->excludes) > 0) {
                $excludes = array_filter($this->excludes, function ($pattern) use ($uri) {
                    return preg_match($pattern, $uri);
                });

                if (count($excludes) > 0) {
                    return;
                }
            }

            $hash = md5(
                $exception->getStatusCode().$uri.$request->headers->get('referer')
            );

            if ($model = $this->em->getRepository(Exception::class)->findOneBy(['hash' => $hash])) {
                $model->increaseEvents();
                $model->setResolved(false);
            } else {
                $model = new Exception();
                $model->setCode($exception->getStatusCode());
                $model->setUrl($uri);
                $model->setUrlReferer($request->headers->get('referer'));
                $model->setHash($hash);
            }
            $this->em->persist($model);
            $this->em->flush();
        }
    }
}
