<?php

namespace Kunstmaan\ViewBundle\Helper;
use Kunstmaan\AdminNodeBundle\Modules\NodeMenu;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Kunstmaan\AdminBundle\Modules\ClassLookup;

/**
 *  a context for rendering pages through service methods
 */
class RenderContext extends \ArrayObject
{

    /**
     * @var string
     */
    private $view;

    /**
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param string $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }
}
