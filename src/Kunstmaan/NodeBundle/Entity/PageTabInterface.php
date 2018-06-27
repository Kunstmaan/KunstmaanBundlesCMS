<?php

namespace Kunstmaan\TabBundle\Entity;

use Kunstmaan\TabBundle\ValueObject\PageTab;

interface PageTabInterface {
	/**
	 * @return PageTab[]
	 */
	public function getTabs();
}