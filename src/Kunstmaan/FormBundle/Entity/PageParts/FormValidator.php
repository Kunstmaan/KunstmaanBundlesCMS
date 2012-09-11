<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;

use Symfony\Component\Form\FormValidatorInterface;
use Symfony\Component\Form\FormInterface;

/**
 * The form validator
 */
class FormValidator implements FormValidatorInterface
{

    private $callback;
    private $object;
    private $uniqueid;

    /**
     * @param mixed    $object   The object to call on
     * @param string   $uniqueid The unique ID
     * @param function $callback The callback
     */
    public function __construct($object, $uniqueid, $callback)
    {
        $this->callback = $callback;
        $this->uniqueid = $uniqueid;
        $this->object = $object;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(FormInterface $form)
    {
        return call_user_func($this->callback, $form, $this->object, $this->uniqueid);
    }
}
