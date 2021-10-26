<?php

namespace Kunstmaan\AdminBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\AdminList\ExceptionAdminListConfigurator;
use Kunstmaan\AdminBundle\Entity\Exception;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @final since 5.9
 */
class ExceptionController extends AdminListController
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

    /**
     * @Route("/", name="kunstmaanadminbundle_admin_exception")
     */
    public function indexAction(Request $request)
    {
        return parent::doIndexAction($this->getAdminListConfigurator(), $request);
    }

    /**
     * @Route("/resolve_all", name="kunstmaanadminbundle_admin_exception_resolve_all", methods={"GET", "POST"})
     *
     * @return RedirectResponse
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \InvalidArgumentException
     */
    public function resolveAllAction(Request $request)
    {
        // NEXT_MAJOR: remove check and change methods property in route annotation
        if ($request->isMethod(Request::METHOD_GET)) {
            @trigger_error(sprintf('Calling the action "%s" with a GET request is deprecated since KunstmaanAdminBundle 5.10 and will only allow a POST request in KunstmaanAdminBundle 6.0.', __METHOD__), E_USER_DEPRECATED);
        }

        $csrfId = 'exception-resolve_all';
        $hasToken = $request->request->has('token');
        // NEXT_MAJOR remove hasToken check and make csrf token required
        if (!$hasToken) {
            @trigger_error(sprintf('Not passing as csrf token with id "%s" in field "token" is deprecated in KunstmaanAdminBundle 5.10 and will be required in KunstmaanAdminBundle 6.0. If you override the adminlist delete action template make sure to post a csrf token.', $csrfId), E_USER_DEPRECATED);
        }

        if ($hasToken && !$this->isCsrfTokenValid($csrfId, $request->request->get('token'))) {
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

    /**
     * @Route("/toggle_resolve/{id}", name="kunstmaanadminbundle_admin_exception_toggle_resolve", methods={"GET", "POST"})
     *
     * @return RedirectResponse
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \InvalidArgumentException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     */
    public function toggleResolveAction(Request $request, Exception $model)
    {
        // NEXT_MAJOR: remove check and change methods property in route annotation
        if ($request->isMethod(Request::METHOD_GET)) {
            @trigger_error(sprintf('Calling the action "%s" with a GET request is deprecated since KunstmaanAdminBundle 5.10 and will only allow a POST request in KunstmaanAdminBundle 6.0.', __METHOD__), E_USER_DEPRECATED);
        }

        $csrfId = 'exception-resolve-item';
        $hasToken = $request->request->has('token');
        // NEXT_MAJOR remove hasToken check and make csrf token required
        if (!$hasToken) {
            @trigger_error(sprintf('Not passing as csrf token with id "%s" in field "token" is deprecated in KunstmaanAdminBundle 5.10 and will be required in KunstmaanAdminBundle 6.0. If you override the adminlist delete action template make sure to post a csrf token.', $csrfId), E_USER_DEPRECATED);
        }

        if ($hasToken && !$this->isCsrfTokenValid($csrfId, $request->request->get('token'))) {
            return new RedirectResponse($this->generateUrl('kunstmaanadminbundle_admin_exception'));
        }

        /* @var EntityManager $em */
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
