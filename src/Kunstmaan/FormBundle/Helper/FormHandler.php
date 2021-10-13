<?php

namespace Kunstmaan\FormBundle\Helper;

use ArrayObject;
use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\EventdispatcherCompatibilityUtil;
use Kunstmaan\FormBundle\Entity\FormAdaptorInterface;
use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\FormBundle\Entity\FormSubmissionField;
use Kunstmaan\FormBundle\Event\FormEvents;
use Kunstmaan\FormBundle\Event\SubmissionEvent;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function handleForm(FormPageInterface $page, Request $request, RenderContext $context)
    {
        /* @var EntityManager $em */
        $em = $this->container->get('doctrine.orm.entity_manager');
        /* @var FormBuilderInterface $formBuilder */
        $formBuilder = $this->container->get('form.factory')->createBuilder(FormType::class);
        /* @var RouterInterface $router */
        $router = $this->container->get('router');
        /* @var ArrayObject $fields */
        $fields = new ArrayObject();
        $pageParts = $em->getRepository('KunstmaanPagePartBundle:PagePartRef')->getPageParts($page, $page->getFormElementsContext());
        foreach ($pageParts as $sequence => $pagePart) {
            if ($pagePart instanceof FormAdaptorInterface) {
                $pagePart->adaptForm($formBuilder, $fields, $sequence);
            }
        }

        $form = $formBuilder->getForm();
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $formSubmission = new FormSubmission();
                $formSubmission->setIpAddress($request->getClientIp());
                $formSubmission->setNode($em->getRepository('KunstmaanNodeBundle:Node')->getNodeFor($page));
                $formSubmission->setLang($request->getLocale());
                $em->persist($formSubmission);

                /* @var FormSubmissionField $field */
                foreach ($fields as $field) {
                    $field->setSubmission($formSubmission);
                    $field->onValidPost($form, $formBuilder, $request, $this->container);
                    $em->persist($field);
                }

                $em->flush();
                $em->refresh($formSubmission);

                $event = new SubmissionEvent($formSubmission, $page);

                $eventDispatcher = EventdispatcherCompatibilityUtil::upgradeEventDispatcher($this->container->get('event_dispatcher'));
                $eventDispatcher->dispatch($event, FormEvents::ADD_SUBMISSION);

                return new RedirectResponse($page->generateThankYouUrl($router, $context));
            }
        }
        $context['frontendform'] = $form->createView();
        $context['frontendformobject'] = $form;

        return null;
    }
}
