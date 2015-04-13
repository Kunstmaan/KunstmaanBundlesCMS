<?php

namespace Kunstmaan\FormBundle\Controller;


use Kunstmaan\NodeBundle\Helper\RenderContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class AbstractFormPageController
 * @package Kunstmaan\FormBundle\Controller
 */
class AbstractFormPageController extends Controller{


    /**
     * @param Request $request
     * @return null
     */
    public function serviceAction(Request $request){

        $context = array();
        $thanksParam = $request->get('thanks');
        if (!empty($thanksParam)) {
            $context["thanks"] = true;

            return null;
        }
        $entity = $request->attributes->get('_entity');

        $renderContext = new RenderContext(
            array(
                'nodetranslation' => $request->attributes->get('_nodeTranslation'),
                'slug'            => $request->attributes->get('url'),
                'page'            => $entity,
                'resource'        => $entity,
                'nodemenu'        => $request->attributes->get('_nodeMenu'),
            )
        );

        $this->container->get('kunstmaan_form.form_handler')->handleForm($entity, $request, $renderContext);

        $request->attributes->set('_renderContext', $renderContext->getArrayCopy());
    }
}