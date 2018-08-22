<?php

namespace Kunstmaan\MediaBundle\Helper\Imagine;

use Symfony\Component\HttpFoundation\Request;

class ImagineController extends \Liip\ImagineBundle\Controller\ImagineController
{
    /**
     * {@inheritdoc}
     */
    public function filterAction(Request $request, $path, $filter)
    {
        if ($request->query->has('originalExtension')) {
            $info = pathinfo($path);
            $path = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '.' . $request->query->getAlpha('originalExtension');
        }

        return parent::filterAction($request, $path, $filter);
    }

    /**
     * {@inheritdoc}
     */
    public function filterRuntimeAction(Request $request, $hash, $path, $filter)
    {
        if ($request->query->has('originalExtension')) {
            $info = pathinfo($path);
            $path = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '.' . $request->query->getAlpha('originalExtension');
        }

        return parent::filterRuntimeAction($request, $hash, $path, $filter);
    }
}
