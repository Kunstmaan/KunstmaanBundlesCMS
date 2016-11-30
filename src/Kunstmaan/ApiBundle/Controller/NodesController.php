<?php

/*
 * This file is part of the KunstmaanBundlesCMS package.
 *
 * (c) Kunstmaan <https://github.com/Kunstmaan/KunstmaanBundlesCMS/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kunstmaan\ApiBundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\ControllerTrait;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class NodesController
 *
 * @author Ruud Denivel <ruud.denivel@kunstmaan.be>
 *
 * @Route(service="kunstmaan_api.controller.nodes")
 */
class NodesController
{
    use ControllerTrait;

    /** @var EntityManager */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Retrieve nodes paginated
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get all nodes",
     *  resourceDescription="Get all nodes",
     *  output="Kunstmaan\NodeBundle\Entity\Node",
     *  statusCodes={
     *      200="Returned when successful",
     *      403="Returned when the user is not authorized to fetch nodes",
     *      500="Something went wrong"
     *  }
     * )
     *
     * @Annotations\QueryParam(name="internalName", nullable=true, description="The internal name of the node")
     * @Annotations\QueryParam(name="hiddenFromNav", nullable=true, default=false, description="If 1, only nodes hidden from nav will be returned")
     * @Annotations\QueryParam(name="refEntityName", nullable=true, description="Which pages you want to have returned")
     */
    public function getNodesAction(ParamFetcherInterface $paramFetcher)
    {
        $params = [];

        foreach ($paramFetcher->all() as $key => $param) {
            if (null !== $param) {
                $params[$key] = $param;
            }
        }

        $data = $this->em->getRepository('KunstmaanNodeBundle:Node')->findBy($params);

        return $this->handleView($this->view($data, Response::HTTP_OK));
    }

    /**
     * Retrieve a single node
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get a node",
     *  resourceDescription="Get a node",
     *  output="Kunstmaan\NodeBundle\Entity\Node",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="The node ID"
     *      }
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      403="Returned when the user is not authorized to fetch nodes",
     *      500="Something went wrong"
     *  }
     * )
     */
    public function getNodeAction($id)
    {
        $data = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);
        return $this->handleView($this->view($data, Response::HTTP_OK));
    }

    /**
     * Retrieve a single node's children
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get a node's children",
     *  resourceDescription="Get a node's children",
     *  output="Kunstmaan\NodeBundle\Entity\Node",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="The node ID"
     *      }
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      403="Returned when the user is not authorized to fetch nodes",
     *      500="Something went wrong"
     *  }
     * )
     */
    public function getNodeChildrenAction($id)
    {
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);
        $data = $node->getChildren();

        return $this->handleView($this->view($data, Response::HTTP_OK));
    }

    /**
     * Retrieve a single node's parent
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get a node's parent",
     *  resourceDescription="Get a node's parent",
     *  output="Kunstmaan\NodeBundle\Entity\Node",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="The node ID"
     *      }
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      403="Returned when the user is not authorized to fetch nodes",
     *      500="Something went wrong"
     *  }
     * )
     */
    public function getNodeParentAction($id)
    {
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);
        $data = $node->getParent();

        return $this->handleView($this->view($data, Response::HTTP_OK));
    }
}