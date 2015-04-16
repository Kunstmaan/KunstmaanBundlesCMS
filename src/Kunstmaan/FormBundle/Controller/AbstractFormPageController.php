<?php

namespace Kunstmaan\FormBundle\Controller;

use Kunstmaan\NodeBundle\Helper\RenderContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AbstractFormPageController extends Controller
{
    /**
     * @param Request $request
     * @return null
     */
    public function serviceAction(Request $request)
    {
        $thanksParam = $request->get('thanks');
        $entity = $request->attributes->get('_entity');
        $context = array(
            'nodetranslation' => $request->attributes->get('_nodeTranslation'),
            'slug'            => $request->attributes->get('url'),
            'page'            => $entity,
            'resource'        => $entity,
            'nodemenu'        => $request->attributes->get('_nodeMenu'),
        );

        if (!empty($thanksParam)) {
            $context['thanks'] = true;
        }

        $renderContext = new RenderContext($context);
        $result = $this->container->get('kunstmaan_form.form_handler')->handleForm($entity, $request, $renderContext);
        if ($result instanceof Response) {
            return $result;
        }

        $request->attributes->set('_renderContext', $renderContext->getArrayCopy());
    }
}
