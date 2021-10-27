<?php

namespace Kunstmaan\LeadGenerationBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\LeadGenerationBundle\Entity\Popup\AbstractPopup;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

abstract class AbstractRedirectController extends AbstractController
{
    /** @var EntityManagerInterface|null */
    private $em;

    public function __construct(EntityManagerInterface $em = null)
    {
        if (null === $em) {
            trigger_deprecation('kunstmaan/lead-generation-bundle', '6.1', 'To passing an instance of "%s" to "%s" is deprecated and will be required in 6.0.', EntityManagerInterface::class, __METHOD__);
        }

        $this->em = $em;
    }

    /**
     * @Route("/{popup}", name="redirect_index", requirements={"popup": "\d+"})
     */
    public function indexAction($popup)
    {
        // NEXT_MAJOR remove getDoctrine fallback
        $em = $this->em ?? $this->getDoctrine();
        /** @var AbstractPopup $thePopup */
        $thePopup = $em->getRepository(AbstractPopup::class)->find($popup);

        return $this->render($this->getIndexTemplate(), [
            'popup' => $thePopup,
        ]);
    }

    protected function getIndexTemplate()
    {
        return '@KunstmaanLeadGeneration/Redirect/index.html.twig';
    }
}
