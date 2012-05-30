<?php

namespace Kunstmaan\PagePartBundle\Entity;

use Kunstmaan\PagePartBundle\Helper\PagePartInterface;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Abstract ORM Pagepart
 */
abstract class AbstractPagePart extends AbstractEntity implements PagePartInterface
{

	/**
	 * In most cases, the backend view will not differ from the default one.
	 * Also, this implementation guarantees backwards compatibility.
	 * @return mixed
	 */
	public function getAdminView()
	{
		return $this->getDefaultView();
	}
}
