<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;

use Symfony\Component\Form\FormValidatorInterface;
use Symfony\Component\Form\FormInterface;

/**
 * The form validator
 */
class FormValidator implements FormValidatorInterface
{

    /**
     * @var mixed The callback function
     */
    private $callback;

    /**
     * @var mixed
     */
    private $object;

    /**
     * @var string
     */
    private $uniqueid;

    /**
     * @param mixed  $object   The object to call on
     * @param string $uniqueid The unique ID
     * @param mixed  $callback The callback function
     */
    public function __construct($object, $uniqueid, $callback)
    {
        $this->callback = $callback;
        $this->uniqueid = $uniqueid;
        $this->object = $object;
    }

    /**
     * Validates the given form
     *
     * @param FormInterface $form
     *
     * @return mixed
     */
    public function validate(FormInterface $form)
    {
        return call_user_func($this->callback, $form, $this->object, $this->uniqueid);
    }
}
