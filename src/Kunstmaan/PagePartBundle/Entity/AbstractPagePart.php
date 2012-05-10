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


}
