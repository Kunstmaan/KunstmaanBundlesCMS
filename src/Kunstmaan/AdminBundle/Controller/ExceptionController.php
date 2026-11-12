<?php

namespace Kunstmaan\AdminBundle\Controller;

use Kunstmaan\AdminBundle\AdminList\ExceptionAdminListConfigurator;
use Kunstmaan\AdminBundle\Entity\Exception;
use Kunstmaan\AdminListBundle\Controller\AbstractAdminListController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class ExceptionController extends AbstractAdminListController
{
    /** @var ExceptionAdminListConfigurator */
    private $configurator;

    private function getAdminListConfigurator()
    {
        if (!isset($this->configurator)) {
            $this->configurator = new ExceptionAdminListConfigurator($this->getEntityManager());
        }

        return $this->configurator;
    }

    #[Route(path: '/', name: 'kunstmaanadminbundle_admin_exception')]
    public function indexAction(Request $request)
    {
        return parent::doIndexAction($this->getAdminListConfigurator(), $request);
    }

    #[Route(path: '/resolve_all', name: 'kunstmaanadminbundle_admin_exception_resolve_all', methods: ['POST'])]
    public function resolveAllAction(Request $request): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('exception-resolve_all', $request->request->get('token'))) {
            return new RedirectResponse($this->generateUrl('kunstmaanadminbundle_admin_exception'));
        }

        $this->getEntityManager()->getRepository(Exception::class)->markAllAsResolved();

        $indexUrl = $this->getAdminListConfigurator()->getIndexUrl();

        return new RedirectResponse(
            $this->generateUrl(
                $indexUrl['path'],
                isset($indexUrl['params']) ? $indexUrl['params'] : []
            )
        );
    }

    #[Route(path: '/toggle_resolve/{id}', name: 'kunstmaanadminbundle_admin_exception_toggle_resolve', methods: ['POST'])]
    public function toggleResolveAction(Request $request, Exception $model): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('exception-resolve-item', $request->request->get('token'))) {
            return new RedirectResponse($this->generateUrl('kunstmaanadminbundle_admin_exception'));
        }

        $em = $this->getEntityManager();

        $this->getAdminListConfigurator();

        $model->setResolved(!$model->isResolved());

        $em->persist($model);
        $em->flush();

        $indexUrl = $this->configurator->getIndexUrl();

        return new RedirectResponse(
            $this->generateUrl(
                $indexUrl['path'],
                isset($indexUrl['params']) ? $indexUrl['params'] : []
            )
        );
    }
}
