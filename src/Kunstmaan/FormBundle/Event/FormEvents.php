<?php

namespace Kunstmaan\FormBundle\Event;

final class FormEvents
{
    /**
     * This event is thrown each time a new form submission gets saved.
     *
     * The event listener receives an Kunstmaan\FormBundle\Event\SubmissionEvent instance.
     *
     * @var string
     */
    const ADD_SUBMISSION = 'kunstmaan_form.add_submission';
}
