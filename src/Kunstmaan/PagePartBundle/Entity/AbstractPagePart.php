<?php

namespace Kunstmaan\PagePartBundle\Entity;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;

use Kunstmaan\PagePartBundle\Helper\IsPagePart;
use Doctrine\ORM\Mapping as ORM;

/**
 * Abstract ORM Pagepart
 */
abstract class AbstractPagePart extends AbstractEntity implements IsPagePart
{


}
