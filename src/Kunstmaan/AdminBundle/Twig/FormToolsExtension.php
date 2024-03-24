<?php

namespace Kunstmaan\AdminBundle\Twig;

use Kunstmaan\AdminBundle\Helper\FormHelper;
use Symfony\Component\Form\FormView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class FormToolsExtension extends AbstractExtension
{
    /**
     * @var FormHelper
     */
    private $formHelper;

    public function __construct(FormHelper $formHelper)
    {
        $this->formHelper = $formHelper;
    }

    /**
     * Get Twig functions defined in this extension.
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('form_errors_recursive', [$this, 'getErrorMessages']),
            new TwigFunction('form_has_errors_recursive', [$this, 'hasErrorMessages']),
        ];
    }

    /**
     * Return if there are error messages.
     */
    public function hasErrorMessages(FormView $formView): bool
    {
        return $this->formHelper->hasRecursiveErrorMessages($formView);
    }

    /**
     * Get the error messages.
     *
     * @param FormView[]|FormView $formViews The form views
     * @param array               &$errors   The errors
     */
    public function getErrorMessages($formViews, array &$errors = []): array
    {
        return $this->formHelper->getRecursiveErrorMessages($formViews, $errors);
    }
}
