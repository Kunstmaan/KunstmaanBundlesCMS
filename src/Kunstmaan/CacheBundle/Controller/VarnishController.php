<?php

namespace Kunstmaan\CacheBundle\Controller;

use Kunstmaan\CacheBundle\Form\Varnish\BanType;
use Kunstmaan\CacheBundle\Helper\VarnishHelper;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class VarnishController extends AbstractController
{
    /** @var VarnishHelper */
    private $varnishHelper;

    public function __construct(VarnishHelper $varnishHelper)
    {
        $this->varnishHelper = $varnishHelper;
    }

    /**
     * Generates the varnish ban form.
     *
     * @Route("/settings/varnish", name="kunstmaancachebundle_varnish_settings_ban")
     */
    public function indexAction(Request $request): Response
    {
        $this->checkPermission();

        $form = $this->createForm(BanType::class);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $path = $form['path']->getData();

                $this->varnishHelper->banPath($path, $form['allDomains']->getData());

                $this->addFlash('success', 'kunstmaan_cache.varnish.ban.success');
            }
        }

        return $this->render('@KunstmaanCache/Varnish/ban.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Ban route from varnish
     *
     * @Route("/varnish/ban/{node}", name="kunstmaancachebundle_varnish_ban")
     *
     * @return RedirectResponse
     */
    public function banAction(Node $node)
    {
        $this->checkPermission();

        /** @var NodeTranslation $nodeTranslation */
        foreach ($node->getNodeTranslations() as $nodeTranslation) {
            $route = $this->generateUrl('_slug', ['url' => $nodeTranslation->getUrl(), '_locale' => $nodeTranslation->getLang()]);
            $this->varnishHelper->banPath($route);
        }
        $this->addFlash('success', 'kunstmaan_cache.varnish.ban.success');

        return $this->redirect($this->generateUrl('KunstmaanNodeBundle_nodes_edit', ['id' => $node->getId()]));
    }

    /**
     * Check permission
     *
     * @param string $roleToCheck
     *
     * @throws AccessDeniedException
     */
    private function checkPermission($roleToCheck = 'ROLE_SUPER_ADMIN')
    {
        if (false === $this->isGranted($roleToCheck)) {
            throw new AccessDeniedException();
        }
    }
}
