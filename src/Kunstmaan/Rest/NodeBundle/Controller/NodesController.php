<?php

/*
 * This file is part of the KunstmaanBundlesCMS package.
 *
 * (c) Kunstmaan <https://github.com/Kunstmaan/KunstmaanBundlesCMS/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kunstmaan\Rest\NodeBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\ControllerTrait;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class NodesController
 *
 * @author Ruud Denivel <ruud.denivel@kunstmaan.be>
 *
 * @Route(service="kunstmaan_api.controller.nodes")
 *
 */
class NodesController
{
    use ControllerTrait;

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Retrieve nodes paginated
     *
     * @SWG\Get(
     *     path="/api/nodes",
     *     description="Get all nodes",
     *     operationId="getNodes",
     *     produces={"application/json"},
     *     tags={"nodes"},
     *     @SWG\Parameter(
     *         name="internalName",
     *         in="query",
     *         type="string",
     *         description="The internal name of the node",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="hiddenFromNav",
     *         in="query",
     *         type="boolean",
     *         description="If 1, only nodes hidden from nav will be returned",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="refEntityName",
     *         in="query",
     *         type="string",
     *         description="Which pages you want to have returned",
     *         required=false,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when successful"
     *     ),
     *     @SWG\Response(
     *         response=403,
     *         description="Returned when the user is not authorized to fetch nodes",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     ),
     *     @SWG\Response(
     *         response="default",
     *         description="unexpected error",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     *
     * @Annotations\QueryParam(name="internalName", nullable=true, description="The internal name of the node")
     * @Annotations\QueryParam(name="hiddenFromNav", nullable=true, default=false, description="If 1, only nodes hidden from nav will be returned")
     * @Annotations\QueryParam(name="refEntityName", nullable=true, description="Which pages you want to have returned")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getNodesAction(ParamFetcher $paramFetcher)
    {
        $params = [];

        foreach ($paramFetcher->all() as $key => $param) {
            if (null !== $param) {
                $params[$key] = $param;
            }
        }

        $data = $this->em->getRepository(Node::class)->findBy($params);

        return $this->handleView($this->view($data, Response::HTTP_OK));
    }

    /**
     * Retrieve a single node
     *
     * @SWG\Get(
     *     path="/api/nodes/{id}",
     *     description="Get a node by ID",
     *     operationId="getNode",
     *     produces={"application/json"},
     *     tags={"nodes"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="query",
     *         type="integer",
     *         description="The node ID",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when successful",
     *         @SWG\Schema(ref="#/definitions/Node")
     *     ),
     *     @SWG\Response(
     *         response=403,
     *         description="Returned when the user is not authorized to fetch nodes",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     ),
     *     @SWG\Response(
     *         response="default",
     *         description="unexpected error",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function getNodeAction($id)
    {
        $data = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);
        return $this->handleView($this->view($data, Response::HTTP_OK));
    }


    /**
     * Retrieve a single node's translations
     *
     * ApiDoc(
     *  resource=true,
     *  description="Retrieve a single node's translations",
     *  resourceDescription="Retrieve a single node's translations",
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
     *
     * @Annotations\QueryParam(name="lang", nullable=true, description="Set language if you want only to retrieve the node translation in this language")
     */
    public function getNodeTranslationsAction($id, ParamFetcherInterface $paramFetcher)
    {
        $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->find($id);

        if ($lang = $paramFetcher->get('lang')) {
            $data = $node->getNodeTranslation($lang);
        }
        else {
            $data = $node->getNodeTranslations();
        }

        return $this->handleView($this->view($data, Response::HTTP_OK));
    }

    /**
     * Retrieve a single node's children
     *
     * ApiDoc(
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
     * ApiDoc(
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