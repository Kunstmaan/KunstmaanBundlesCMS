<?php

namespace Kunstmaan\LeadGenerationBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\LeadGenerationBundle\Entity\Popup\AbstractPopup;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

abstract class AbstractRedirectController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(?EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route(path: '/{popup}', name: 'redirect_index', requirements: ['popup' => '\d+'])]
    public function indexAction($popup)
    {
        /** @var AbstractPopup $thePopup */
        $thePopup = $this->em->getRepository(AbstractPopup::class)->find($popup);

        return $this->render($this->getIndexTemplate(), [
            'popup' => $thePopup,
        ]);
    }

    protected function getIndexTemplate()
    {
        return '@KunstmaanLeadGeneration/Redirect/index.html.twig';
    }
}
