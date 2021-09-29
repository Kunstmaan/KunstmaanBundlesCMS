<?php

declare(strict_types=1);

namespace Kunstmaan\FormBundle\ViewDataProvider;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\FormBundle\Helper\FormHandlerInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\PageViewDataProviderInterface;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

final class FormPageViewDataProvider implements PageViewDataProviderInterface
{
    /** @var RequestStack */
    private $requestStack;
    /** @var FormHandlerInterface */
    private $formHandler;
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(RequestStack $requestStack, FormHandlerInterface $formHandler, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->formHandler = $formHandler;
        $this->em = $em;
    }

    public function provideViewData(NodeTranslation $nodeTranslation, RenderContext $renderContext): void
    {
        $request = $this->requestStack->getMasterRequest();
        if (null === $request) {
            return;
        }

        $thanksParam = $request->get('thanks');
        $entity = $nodeTranslation->getRef($this->em);
        $renderContext['nodetranslation'] = $nodeTranslation;
        $renderContext['slug'] = $request->attributes->get('url');
        $renderContext['page'] = $entity;
        $renderContext['resource'] = $entity;

        if (!empty($thanksParam)) {
            $renderContext['thanks'] = true;
        }

        $result = $this->formHandler->handleForm($entity, $request, $renderContext);
        if ($result instanceof Response) {
            $renderContext->setResponse($result);
        }
    }
}
