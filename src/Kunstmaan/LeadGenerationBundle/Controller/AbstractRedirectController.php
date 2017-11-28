<?php

namespace Kunstmaan\LeadGenerationBundle\Controller;

use Kunstmaan\AdminBundle\Traits\DependencyInjection\EntityManagerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class AbstractRedirectController extends AbstractController
{
    use EntityManagerTrait;

    /**
     * @Route("/{popup}", name="redirect_index", requirements={"popup": "\d+"})
     */
    public function indexAction($popup)
    {
        /** @var \Kunstmaan\LeadGenerationBundle\Entity\Popup\AbstractPopup $thePopup */
        $thePopup = $this->getEntityManager()->getRepository('KunstmaanLeadGenerationBundle:Popup\AbstractPopup')->find($popup);

        return $this->render($this->getIndexTemplate(), array(
            'popup' => $thePopup
        ));
    }

    protected function getIndexTemplate()
    {
        return 'KunstmaanLeadGenerationBundle:Redirect:index.html.twig';
    }
}
