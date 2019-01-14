<?php

namespace Kunstmaan\FormBundle\Helper;

use Kunstmaan\NodeBundle\Helper\RenderContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * The form handler handles everything from creating the form to handling the submitted form
 */
interface FormHandlerInterface
{
    /**
     * @param FormPageInterface $page    The form page
     * @param Request           $request The request
     * @param RenderContext     $context The render context
     *
     * @return RedirectResponse|void|null
     */
    public function handleForm(FormPageInterface $page, Request $request, RenderContext $context);
}
