<?php

namespace Kunstmaan\AdminBundle\Twig\Extension;

use Symfony\Component\Locale\Locale;

class FormToolsExtension extends \Twig_Extension
{

    public function getFunctions()
    {
        return array(
            'form_errors_recursive'      => new \Twig_Function_Method($this, 'getErrorMessages'),
            'form_has_errors_recursive'  => new \Twig_Function_Method($this, 'hasErrorMessages'),
        );
    }

    public function getName()
    {
        return 'FormToolsExtension';
    }

    public function hasErrorMessages(\Symfony\Component\Form\FormView $formView)
    {
        $errors = array();

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

    public function getErrorMessages($formViews, &$errors = array())
    {
        if (is_array($formViews)) {
            foreach ($formViews as $formView) {
                $this->getErrorMessages($formView, $errors);
            }
        } else {
            /**
             * @var \Symfony\Component\Form\FormView $formViews
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

