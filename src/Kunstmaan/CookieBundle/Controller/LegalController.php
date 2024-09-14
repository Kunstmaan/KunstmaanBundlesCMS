<?php

namespace Kunstmaan\CookieBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\CookieBundle\Entity\CookieType;
use Kunstmaan\CookieBundle\Helper\LegalCookieHelper;
use Kunstmaan\NodeBundle\Entity\Node;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class LegalController extends AbstractController
{
    /** @var LegalCookieHelper */
    private $cookieHelper;
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(LegalCookieHelper $cookieHelper, EntityManagerInterface $em)
    {
        $this->cookieHelper = $cookieHelper;
        $this->em = $em;
    }

    #[Route(path: '/modal/{internal_name}', name: 'kunstmaancookiebundle_legal_modal')]
    public function switchTabAction(
        Request $request,
        #[MapEntity(expr: 'repository.getNodeByInternalName(internal_name)')] Node $node,
    ): Response {
        $page = $node->getNodeTranslation($request->getLocale())->getRef($this->em);

        return $this->render(
            '@KunstmaanCookie/CookieBar/_modal.html.twig',
            [
                'node' => $node,
                'page' => $page,
            ]
        );
    }

    #[Route(path: '/detail/{internalName}', name: 'kunstmaancookiebundle_legal_detail', methods: ['GET'], condition: 'request.isXmlHttpRequest()')]
    public function cookieDetailAction(#[MapEntity(mapping: ['internalName' => 'internalName'])] CookieType $cookieType): Response
    {
        return $this->render(
            '@KunstmaanCookie/CookieBar/_detail.html.twig',
            [
                'type' => $cookieType,
            ]
        );
    }

    #[Route(path: '/toggle-cookies', name: 'kunstmaancookiebundle_legal_toggle_cookies')]
    public function toggleCookiesAction(Request $request): JsonResponse
    {
        $cookieTypes = $request->request->all();

        $legalCookie = $this->cookieHelper->findOrCreateLegalCookie($request);

        foreach ($cookieTypes as $internalName => $value) {
            $legalCookie['cookies'][$internalName] = $value;
        }

        $response = new JsonResponse();
        $response->headers->setCookie($this->cookieHelper->saveLegalCookie($request, $legalCookie));

        return $response;
    }

    #[Route(path: '/toggle-all-cookies', name: 'kunstmaancookiebundle_legal_toggle_all_cookies')]
    public function toggleAllCookiesAction(Request $request): JsonResponse
    {
        $legalCookie = $this->cookieHelper->findOrCreateLegalCookie($request);

        foreach ($legalCookie['cookies'] as $internalName => $value) {
            $legalCookie['cookies'][$internalName] = 'true';
        }

        $response = new JsonResponse();
        $response->headers->setCookie($this->cookieHelper->saveLegalCookie($request, $legalCookie));

        return $response;
    }
}
