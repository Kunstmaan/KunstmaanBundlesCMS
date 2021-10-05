<?php

namespace Kunstmaan\LeadGenerationBundle\Controller;

use Kunstmaan\LeadGenerationBundle\Entity\Popup\AbstractPopup;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

abstract class AbstractRedirectController extends AbstractController
{
    /**
     * @Route("/{popup}", name="redirect_index", requirements={"popup": "\d+"})
     */
    public function indexAction($popup)
    {
        /** @var AbstractPopup $thePopup */
        $thePopup = $this->getDoctrine()->getRepository(AbstractPopup::class)->find($popup);

        return $this->render($this->getIndexTemplate(), [
            'popup' => $thePopup,
        ]);
    }

    protected function getIndexTemplate()
    {
        return '@KunstmaanLeadGeneration/Redirect/index.html.twig';
    }
}
