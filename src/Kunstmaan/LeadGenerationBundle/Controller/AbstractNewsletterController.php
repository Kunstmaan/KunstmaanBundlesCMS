<?php

namespace Kunstmaan\LeadGenerationBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\LeadGenerationBundle\Entity\Popup\AbstractPopup;
use Kunstmaan\LeadGenerationBundle\Form\NewsletterSubscriptionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

abstract class AbstractNewsletterController extends AbstractController
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
     * @Route("/{popup}", name="popup_newsletter_index", requirements={"popup": "\d+"})
     */
    public function indexAction($popup)
    {
        // NEXT_MAJOR remove getDoctrine fallback
        $em = $this->em ?? $this->getDoctrine();
        /** @var AbstractPopup $thePopup */
        $thePopup = $em->getRepository(AbstractPopup::class)->find($popup);
        $form = $this->createSubscriptionForm($thePopup);

        return $this->render($this->getIndexTemplate(), [
            'popup' => $thePopup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{popup}/subscribe", name="popup_newsletter_subscribe", requirements={"popup": "\d+"}, methods={"POST"})
     * @Template()
     */
    public function subscribeAction(Request $request, $popup)
    {
        // NEXT_MAJOR remove getDoctrine fallback
        $em = $this->em ?? $this->getDoctrine();
        /** @var AbstractPopup $thePopup */
        $thePopup = $em->getRepository(AbstractPopup::class)->find($popup);
        $form = $this->createSubscriptionForm($thePopup);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleSubscription($request, $form->getData(), $thePopup);

            return $this->render($this->getThanksTemplate(), [
                'popup' => $thePopup,
            ]);
        }

        return $this->render($this->getFormTemplate(), [
            'popup' => $thePopup,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    protected function createSubscriptionForm(AbstractPopup $popup)
    {
        $form = $this->createForm($this->getSubscriptionFormType(), null, [
            'method' => 'POST',
            'action' => $this->generateUrl('popup_newsletter_subscribe', ['popup' => $popup->getId()]),
        ]);
        $form->add('submit', SubmitType::class, [
            'attr' => ['class' => $popup->getHtmlId() . '--submit'],
        ]);

        return $form;
    }

    protected function getSubscriptionFormType()
    {
        return NewsletterSubscriptionType::class;
    }

    protected function getIndexTemplate()
    {
        return '@KunstmaanLeadGeneration/Newsletter/index.html.twig';
    }

    protected function getFormTemplate()
    {
        return '@KunstmaanLeadGeneration/Newsletter/form.html.twig';
    }

    protected function getThanksTemplate()
    {
        return '@KunstmaanLeadGeneration/Newsletter/thanks.html.twig';
    }

    /**
     * @param array $data
     */
    protected function handleSubscription(Request $request, $data, AbstractPopup $popup)
    {
        // Implement you own logic here
    }
}
