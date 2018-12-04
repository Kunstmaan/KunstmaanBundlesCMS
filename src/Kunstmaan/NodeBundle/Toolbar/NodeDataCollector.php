<?php

namespace Kunstmaan\NodeBundle\Toolbar;

use Kunstmaan\AdminBundle\Helper\Toolbar\AbstractDataCollector;
use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NodeDataCollector extends AbstractDataCollector
{
    /**
     * @var NodeMenu
     */
    private $nodeMenu;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * NodeDataCollector constructor.
     *
     * @param NodeMenu              $nodeMenu
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(NodeMenu $nodeMenu, UrlGeneratorInterface $urlGenerator)
    {
        $this->nodeMenu = $nodeMenu;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @return array
     */
    public function getAccessRoles()
    {
        return ['ROLE_ADMIN'];
    }

    /**
     * @return array|null
     */
    public function collectData()
    {
        $current = $this->nodeMenu->getCurrent();

        if ($current) {
            $id = $current->getNode()->getId();

            $route = $this->urlGenerator->generate('KunstmaanNodeBundle_nodes_edit', ['id' => $id]);

            $data = [
                'route' => $route,
                'nt' => $current->getNodeTranslation(),
            ];

            return ['data' => $data];
        }

        return [];
    }

    /**
     * @param Request         $request
     * @param Response        $response
     * @param \Exception|null $exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        if (!$this->showDataCollection($request, $response) || !$this->isEnabled()) {
            $this->data = false;
        } else {
            $this->data = $this->collectData();
        }
    }

    /**
     * Gets the data for template
     *
     * @return array The request events
     */
    public function getTemplateData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kuma_node';
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    public function reset()
    {
        $this->data = [];
    }
}
