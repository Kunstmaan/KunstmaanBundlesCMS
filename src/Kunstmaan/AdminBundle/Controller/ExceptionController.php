<?php

namespace Kunstmaan\AdminBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\AdminList\ExceptionAdminListConfigurator;
use Kunstmaan\AdminBundle\Entity\Exception;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ExceptionController extends AdminListController
{
    private function getAdminListConfigurator()
    {
        if ( !isset($this->configurator) ) {
            $this->configurator = new ExceptionAdminListConfigurator($this->getEntityManager());
        }

        return $this->configurator;
    }

    /**
     * @Route("/", name="kunstmaanadminbundle_admin_exception")
     */
    public function indexAction(Request $request)
    {
        return parent::doIndexAction($this->getAdminListConfigurator(), $request);
    }

    /**
     * @Route("/toggle_resolve/{id}", name="kunstmaanadminbundle_admin_exception_toggle_resolve")
     */
    public function toggleResolveAction(Request $request, Exception $model)
    {
        /* @var EntityManager $em */
        $em = $this->getEntityManager();

        $this->getAdminListConfigurator();

        $model->setResolved( !$model->isResolved() );

        $em->persist($model);
        $em->flush();

        $indexUrl = $this->configurator->getIndexUrl();

        return new RedirectResponse(
            $this->generateUrl(
                $indexUrl['path'],
                isset($indexUrl['params']) ? $indexUrl['params'] : array()
            )
        );
    }
}
