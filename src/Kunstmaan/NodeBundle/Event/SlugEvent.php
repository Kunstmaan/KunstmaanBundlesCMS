<?php

namespace Kunstmaan\NodeBundle\Event;

use Kunstmaan\AdminBundle\Event\BcEvent;
use Kunstmaan\NodeBundle\Entity\CustomViewDataProviderInterface;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Symfony\Component\HttpFoundation\Response;

/**
 * @deprecated The "Kunstmaan\NodeBundle\Event\SlugEvent" class and the related "kunstmaan_node.preSlugAction" and "kunstmaan_node.postSlugAction" events are deprecated since KunstmaanNodeBundle 5.9 and will be removed in KunstmaanNodeBundle 6.0. Implement the "Kunstmaan\NodeBundle\Entity\CustomViewDataProviderInterface" interface on the page entity and provide a render service instead.
 *
 * @final since 5.9
 */
class SlugEvent extends BcEvent
{
    /**
     * @var Response|null
     */
    protected $response;

    /**
     * @var RenderContext
     */
    protected $renderContext;

    public function __construct(?Response $response, RenderContext $renderContext /*, $triggerDeprecation = true */)
    {
        if (func_num_args() === 2) {
            @trigger_error(sprintf('The "%s" class and the related "%s" and "%s" events are deprecated since KunstmaanNodeBundle 5.9 and will be removed in KunstmaanNodeBundle 6.0. Implement the "%s" interface on the page entity and provide a render service instead.', SlugEvent::class, Events::PRE_SLUG_ACTION, Events::POST_SLUG_ACTION, CustomViewDataProviderInterface::class), E_USER_DEPRECATED);
        }

        $this->response = $response;
        $this->renderContext = $renderContext;
    }

    /**
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return RenderContext
     */
    public function getRenderContext()
    {
        return $this->renderContext;
    }

    public function setRenderContext(RenderContext $renderContext)
    {
        $this->renderContext = $renderContext;
    }
}
