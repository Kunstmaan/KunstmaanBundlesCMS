<?php

namespace Kunstmaan\FormBundle\Helper;

use ArrayObject;
use Doctrine\ORM\EntityManager;
use Kunstmaan\FormBundle\Event\FormEvents;
use Kunstmaan\FormBundle\Event\SubmissionEvent;
use Kunstmaan\FormBundle\Entity\FormAdaptorInterface;
use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\FormBundle\Entity\FormSubmissionField;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * The form handler handles everything from creating the form to handling the submitted form
 */
class FormHandler implements FormHandlerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param FormPageInterface $page    The form page
     * @param Request           $request The request
     * @param RenderContext     $context The render context
     *
     * @return RedirectResponse|void|null
     */
    public function handleForm(FormPageInterface $page, Request $request, RenderContext $context)
    {
        /* @var $em EntityManager */
        $em = $this->container->get('doctrine.orm.entity_manager');
        /* @var $formBuilder FormBuilderInterface */
        $formBuilder = $this->container->get('form.factory')->createBuilder('form');
        /* @var $router RouterInterface */
        $router = $this->container->get('router');
        /* @var $fields ArrayObject */
        $fields = new ArrayObject();
        $pageParts = $em->getRepository('KunstmaanPagePartBundle:PagePartRef')->getPageParts($page, $page->getFormElementsContext());
        foreach ($pageParts as $pagePart) {
            if ($pagePart instanceof FormAdaptorInterface) {
                $pagePart->adaptForm($formBuilder, $fields);
            }
        }

        $form = $formBuilder->getForm();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $formSubmission = new FormSubmission();
                $formSubmission->setIpAddress($request->getClientIp());
                $formSubmission->setNode($em->getRepository('KunstmaanNodeBundle:Node')->getNodeFor($page));
                $formSubmission->setLang($locale = $request->getLocale());
                $em->persist($formSubmission);

                /* @var $field FormSubmissionField */
                foreach ($fields as $field) {
                    $field->setSubmission($formSubmission);
                    $field->onValidPost($form, $formBuilder, $request, $this->container);
                    $em->persist($field);
                }

                $em->flush();
                $em->refresh($formSubmission);

                $event = new SubmissionEvent($formSubmission, $page);
                $this->container->get('event_dispatcher')->dispatch(FormEvents::ADD_SUBMISSION, $event);

                $from = $page->getFromEmail();
                $to = $page->getToEmail();
                $subject = $page->getSubject();
                if (!empty($from) && !empty($to) && !empty($subject)) {
                    $mailer = $this->container->get('kunstmaan_form.form_mailer');
                    $mailer->sendContactMail($formSubmission, $from, $to, $subject);
                }

                return new RedirectResponse($page->generateThankYouUrl($router, $context));
            }
        }
        $context["frontendform"] = $form->createView();
        $context["frontendformobject"] = $form;

        return null;
    }
}
