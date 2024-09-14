<?php

namespace Kunstmaan\FormBundle\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\FormBundle\Entity\FormAdaptorInterface;
use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\FormBundle\Entity\FormSubmissionField;
use Kunstmaan\FormBundle\Event\FormEvents;
use Kunstmaan\FormBundle\Event\SubmissionEvent;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\PagePartBundle\Entity\PagePartRef;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * The form handler handles everything from creating the form to handling the submitted form
 */
class FormHandler implements FormHandlerInterface
{
    private ContainerInterface $container;
    private EntityManagerInterface $em;
    private FormFactoryInterface $formFactory;
    private RouterInterface $router;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ContainerInterface $container,
        ?EntityManagerInterface $em = null,
        ?FormFactoryInterface $formFactory = null,
        ?RouterInterface $router = null,
        ?EventDispatcherInterface $eventDispatcher = null,
    ) {
        $this->container = $container;

        if (null === $em) {
            trigger_deprecation('kunstmaan/form-bundle', '6.3', 'Not passing the required services to "%s" is deprecated and those parameters will be required in 7.0. Injected the required services in the constructor instead.', __METHOD__);

            $this->em = $this->container->get('doctrine.orm.entity_manager');
            $this->formFactory = $this->container->get('form.factory');
            $this->router = $this->container->get('router');
            $this->eventDispatcher = $this->container->get('event_dispatcher');

            return;
        }

        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function handleForm(FormPageInterface $page, Request $request, RenderContext $context)
    {
        $formBuilder = $this->formFactory->createBuilder(FormType::class);
        $fields = new \ArrayObject();
        $pageParts = $this->em->getRepository(PagePartRef::class)->getPageParts($page, $page->getFormElementsContext());
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
                $formSubmission->setNode($this->em->getRepository(Node::class)->getNodeFor($page));
                $formSubmission->setLang($request->getLocale());
                $this->em->persist($formSubmission);

                /* @var FormSubmissionField $field */
                foreach ($fields as $field) {
                    $field->setSubmission($formSubmission);
                    $field->onValidPost($form, $formBuilder, $request, $this->container);
                    $this->em->persist($field);
                }

                $this->em->flush();
                $this->em->refresh($formSubmission);

                $event = new SubmissionEvent($formSubmission, $page);
                $this->eventDispatcher->dispatch($event, FormEvents::ADD_SUBMISSION);

                return new RedirectResponse($page->generateThankYouUrl($this->router, $context));
            }
        }
        $context['frontendform'] = $form->createView();
        $context['frontendformobject'] = $form;

        return null;
    }
}
