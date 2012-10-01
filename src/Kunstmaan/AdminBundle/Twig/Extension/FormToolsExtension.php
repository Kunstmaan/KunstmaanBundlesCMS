<?php

namespace Kunstmaan\AdminBundle\Twig\Extension;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormError;

/**
 * FormToolsExtension
 */
class FormToolsExtension extends \Twig_Extension
{

    /**
     * Get Twig functions defined in this extension.
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'form_errors_recursive'      => new \Twig_Function_Method($this, 'getErrorMessages'),
            'form_has_errors_recursive'  => new \Twig_Function_Method($this, 'hasErrorMessages'),
        );
    }

    /**
     * Get the Twig extension name.
     *
     * @return string
     */
    public function getName()
    {
        return 'FormToolsExtension';
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
        if (!empty($formView->vars['errors'])) {
            return true;
        }
        if ($formView->count()) {
            foreach ($formView->children as $child) {
                if ($this->hasErrorMessages($child)) {
                    return true;
                }
            }
        }

        return false;
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
        if (is_array($formViews)) {
            foreach ($formViews as $formView) {
                $this->getErrorMessages($formView, $errors);
            }
        } else {
            /**
             * @var $formViews FormView
             * @var $error     FormError
             */
            foreach ($formViews->vars['errors'] as $error) {

                $template   = $error->getMessageTemplate();
                $parameters = $error->getMessageParameters();

                foreach ($parameters as $var => $value) {
                    $template = str_replace($var, $value, $template);
                }

                $errors[] = $error;
            }
            if ($formViews->count()) {
                foreach ($formViews->children as $child) {
                    $this->getErrorMessages($child, $errors);
                }
            }
        }

        return $errors;
    }

}
