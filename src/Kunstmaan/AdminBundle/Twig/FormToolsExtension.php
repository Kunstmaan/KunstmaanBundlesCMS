<?php

namespace Kunstmaan\AdminBundle\Twig;

use Kunstmaan\AdminBundle\Helper\FormHelper;
use Symfony\Component\Form\FormView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * FormToolsExtension
 *
 * @final since 5.4
 */
class FormToolsExtension extends AbstractExtension
{
    /**
     * @var FormHelper
     */
    private $formHelper;

    /**
     * @param FormHelper $formHelper
     */
    public function __construct(FormHelper $formHelper)
    {
        $this->formHelper = $formHelper;
    }

    /**
     * Get Twig functions defined in this extension.
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('form_errors_recursive', array($this, 'getErrorMessages')),
            new TwigFunction('form_has_errors_recursive', array($this, 'hasErrorMessages')),
        );
    }

    /**
     * Return if there are error messages.
     *
     * @param FormView $formView
     *
     * @return bool
     */
    public function hasErrorMessages(FormView $formView)
    {
        return $this->formHelper->hasRecursiveErrorMessages($formView);
    }

    /**
     * Get the error messages.
     *
     * @param FormView[]|FormView $formViews The form views
     * @param array               &$errors   The errors
     *
     * @return array
     */
    public function getErrorMessages($formViews, array &$errors = array())
    {
        return $this->formHelper->getRecursiveErrorMessages($formViews, $errors);
    }
}
