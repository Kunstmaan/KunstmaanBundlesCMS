<?php

namespace Kunstmaan\AdminBundle\Twig\Extension;

use Symfony\Component\Form\FormView;

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
        foreach ($formView->get('errors') as $error) {
            return true;
        }
        if ($formView->hasChildren()) {
            foreach ($formView->getChildren() as $child) {
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
     * @param object|FormView[] $formViews
     * @param array             $errors
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
             * @var FormView $formViews
             */
            foreach ($formViews->get('errors') as $error) {

                $template   = $error->getMessageTemplate();
                $parameters = $error->getMessageParameters();

                foreach ($parameters as $var => $value) {
                    $template = str_replace($var, $value, $template);
                }

                $errors[] = $error;
            }
            if ($formViews->hasChildren()) {
                foreach ($formViews->getChildren() as $child) {
                    $this->getErrorMessages($child, $errors);
                }
            }
        }

        return $errors;
    }

}
