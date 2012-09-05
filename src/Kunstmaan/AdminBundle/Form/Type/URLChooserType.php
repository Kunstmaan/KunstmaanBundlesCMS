<?php

namespace Kunstmaan\AdminBundle\Form\Type;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityChoiceList;
use Symfony\Bridge\Doctrine\Form\EventListener\MergeCollectionListener;
use Symfony\Bridge\Doctrine\Form\DataTransformer\EntitiesToArrayTransformer;
use Symfony\Bridge\Doctrine\Form\DataTransformer\EntityToIdTransformer;
use Symfony\Component\Form\AbstractType;

class URLChooserType extends AbstractType {
	protected $objectManager;

	public function __construct($objectManager) {
		$this->objectManager = $objectManager;
	}

	public function getDefaultOptions(array $options) {
		return $options;
	}

	public function getParent() {
		return 'text';
	}

	public function getName() {
		return 'urlchooser';
	}
}
