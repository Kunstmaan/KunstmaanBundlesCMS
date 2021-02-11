<?php

namespace Kunstmaan\CacheBundle\Controller;

use Kunstmaan\CacheBundle\Form\Varnish\BanType;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class VarnishController extends Controller
{
    /**
     * Generates the varnish ban form.
     *
     * @Route("/settings/varnish", name="kunstmaancachebundle_varnish_settings_ban")
     * @Template("KunstmaanCacheBundle:Varnish:ban.html.twig")
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        $this->checkPermission();

        $form = $this->createForm(BanType::class);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $path = $form['path']->getData();

                $this->get('kunstmaan_cache.helper.varnish')->banPath($path, $form['allDomains']->getData());

                $this->addFlash('success', 'kunstmaan_cache.varnish.ban.success');
            }
        }

        return [
            'form' => $form->createView(),
        ];
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
            $this->get('kunstmaan_cache.helper.varnish')->banPath($route);
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
