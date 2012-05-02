<?php

namespace Kunstmaan\FormBundle\Entity;

use Symfony\Component\Form\FormBuilder;

interface FormAdaptorIFace {

	public function adaptForm(FormBuilder $formBuilder, &$fields);

}
