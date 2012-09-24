<?php

namespace Kunstmaan\FormBundle\Helper;

use Kunstmaan\FormBundle\Entity\AbstractFormPage;
use Kunstmaan\NodeBundle\Helper\RenderContext;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The form handler handles everything from creating the form to handling the submitted form
 */
interface FormHandlerInterface
{

    /**
     * @param AbstractFormPage $page    The form page
     * @param Request          $request The request
     * @param RenderContext    $context The render context
     *
     * @return RedirectResponse|void|null
     */
    public function handleForm(AbstractFormPage $page, Request $request, RenderContext $context);

}
