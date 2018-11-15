<?php

namespace Kunstmaan\LeadGenerationBundle\Controller;

use Kunstmaan\LeadGenerationBundle\Entity\Popup\AbstractPopup;
use Kunstmaan\LeadGenerationBundle\Form\NewsletterSubscriptionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractNewsletterController extends Controller
{
    /**
     * @Route("/{popup}", name="popup_newsletter_index", requirements={"popup": "\d+"})
     */
    public function indexAction($popup)
    {
        /** @var \Kunstmaan\LeadGenerationBundle\Entity\Popup\AbstractPopup $thePopup */
        $thePopup = $this->getDoctrine()->getRepository('KunstmaanLeadGenerationBundle:Popup\AbstractPopup')->find($popup);
        $form = $this->createSubscriptionForm($thePopup);

        return $this->render($this->getIndexTemplate(), array(
            'popup' => $thePopup,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{popup}/subscribe", name="popup_newsletter_subscribe", requirements={"popup": "\d+"})
     * @Method("POST")
     * @Template()
     */
    public function subscribeAction(Request $request, $popup)
    {
        /** @var \Kunstmaan\LeadGenerationBundle\Entity\Popup\AbstractPopup $thePopup */
        $thePopup = $this->getDoctrine()->getRepository('KunstmaanLeadGenerationBundle:Popup\AbstractPopup')->find($popup);
        $form = $this->createSubscriptionForm($thePopup);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleSubscription($request, $form->getData(), $thePopup);

            return $this->render($this->getThanksTemplate(), array(
                'popup' => $thePopup,
            ));
        }

        return $this->render($this->getFormTemplate(), array(
            'popup' => $thePopup,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param AbstractPopup $popup
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createSubscriptionForm(AbstractPopup $popup)
    {
        $form = $this->createForm($this->getSubscriptionFormType(), null, array(
            'method' => 'POST',
            'action' => $this->generateUrl('popup_newsletter_subscribe', array('popup' => $popup->getId())),
        ));
        $form->add('submit', SubmitType::class, array(
            'attr' => array('class' => $popup->getHtmlId() . '--submit'),
        ));

        return $form;
    }

    protected function getSubscriptionFormType()
    {
        return NewsletterSubscriptionType::class;
    }

    protected function getIndexTemplate()
    {
        return 'KunstmaanLeadGenerationBundle:Newsletter:index.html.twig';
    }

    protected function getFormTemplate()
    {
        return 'KunstmaanLeadGenerationBundle:Newsletter:form.html.twig';
    }

    protected function getThanksTemplate()
    {
        return 'KunstmaanLeadGenerationBundle:Newsletter:thanks.html.twig';
    }

    /**
     * @param Request       $request
     * @param array         $data
     * @param AbstractPopup $popup
     */
    protected function handleSubscription(Request $request, $data, AbstractPopup $popup)
    {
        // Implement you own logic here
    }
}
