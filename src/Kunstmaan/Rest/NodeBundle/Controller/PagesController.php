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
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\ControllerTrait;
use FOS\RestBundle\Request\ParamFetcher;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Helper\NodeHelper;
use Kunstmaan\Rest\CoreBundle\Controller\AbstractApiController;
use Kunstmaan\Rest\CoreBundle\Helper\DataTransformerTrait;
use Kunstmaan\Rest\CoreBundle\Service\DataTransformerService;
use Kunstmaan\Rest\NodeBundle\Model\ApiPage;
use Kunstmaan\Rest\NodeBundle\Service\Helper\PagePartHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class PagesController
 */
class PagesController extends AbstractApiController
{
    use ControllerTrait;
    use DataTransformerTrait;

    /** @var EntityManagerInterface */
    private $em;

    /** @var DataTransformerService */
    private $dataTransformer;

    /** @var NodeHelper */
    private $nodeHelper;

    /** @var PagePartHelper */
    private $pagePartHelper;

    /**
     * PagesController constructor.
     *
     * @param EntityManagerInterface $em
     * @param DataTransformerService $dataTransformer
     * @param NodeHelper             $nodeHelper
     * @param PagePartHelper         $pagePartHelper
     */
    public function __construct(
        EntityManagerInterface $em,
        DataTransformerService $dataTransformer,
        NodeHelper $nodeHelper,
        PagePartHelper $pagePartHelper
    ) {
        $this->em = $em;
        $this->dataTransformer = $dataTransformer;
        $this->nodeHelper = $nodeHelper;
        $this->pagePartHelper = $pagePartHelper;
    }

    /**
     * Retrieve nodes paginated
     *
     * @SWG\Get(
     *     path="/api/pages",
     *     description="Get a pages of a certain type",
     *     operationId="getPages",
     *     produces={"application/json"},
     *     tags={"pages"},
     *     @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         type="integer",
     *         description="The current page",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="limit",
     *         in="query",
     *         type="integer",
     *         description="Amount of results (default 20)",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="type",
     *         in="query",
     *         type="string",
     *         description="The FQCN of the page",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="locale",
     *         in="query",
     *         type="string",
     *         description="The language of your content",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when successful",
     *         @SWG\Schema(ref="#/definitions/ApiPage")
     *     ),
     *     @SWG\Response(
     *         response=403,
     *         description="Returned when the user is not authorized to fetch nodes",
     *         @SWG\Schema(ref="#/definitions/Node")
     *     ),
     *     @SWG\Response(
     *         response="default",
     *         description="unexpected error",
     *         @SWG\Schema(ref="#/definitions/Node")
     *     )
     * )
     *
     * @QueryParam(name="page", nullable=false, default="1", requirements="\d+", description="The current page")
     * @QueryParam(name="limit", nullable=false, default="20", requirements="\d+", description="Amount of results")
     *
     * @param Request      $request
     * @param ParamFetcher $paramFetcher
     *
     * @return Response
     */
    public function getPagesAction(Request $request, ParamFetcher $paramFetcher)
    {
        if (!$request->query->has('locale')) {
            throw new HttpException(400, "Missing locale");
        }

        // TODO: validate query params
        $page = $request->query->getInt('page');
        $limit = $request->query->getInt('limit');
        $type = $request->query->get('type');
        $locale = $request->query->get('locale');

        $qb = $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')->getOnlineNodeTranslationsQueryBuilder($locale)
            ->andWhere('v.refEntityName = :refEntityName')
            ->setParameter('refEntityName', $type);

        $paginatedCollection = $this->createORMPaginatedCollection($qb, $page, $limit, $this->createTransformerDecorator());

        return $this->handleView($this->view($paginatedCollection, Response::HTTP_OK));
    }

    /**
     * Get a page by node translation ID
     *
     * @SWG\Get(
     *     path="/api/pages/{id}",
     *     description="Get a page by node translation ID",
     *     operationId="getPage",
     *     produces={"application/json"},
     *     tags={"pages"},
     *     @SWG\Parameter(
     *         name="locale",
     *         in="query",
     *         type="string",
     *         description="The language of your content",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         description="The node translation ID",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returned when successful",
     *         @SWG\Schema(ref="#/definitions/ApiPage")
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
    public function getPageAction(Request $request, $id)
    {
        if (!$request->query->has('locale')) {
            throw new HttpException(400, "Missing locale");
        }

        $locale = $request->getLocale();

        $qb = $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')->getOnlineNodeTranslationsQueryBuilder($locale)
            ->andWhere('nt.id = :id')
            ->setParameter('id', $id);

        $nodeTranslation = $qb->getQuery()->getOneOrNullResult();

        if (!$nodeTranslation instanceof NodeTranslation) {
            throw new NotFoundHttpException();
        }

        $data = $this->dataTransformer->transform($nodeTranslation);

        return $this->handleView($this->view($data, Response::HTTP_OK));
    }

    /**
     * Update a ApiPage
     *
     * @View(
     *     statusCode=204
     * )
     *
     * @Rest\Route(path="/pages/{id}")
     *
     * @SWG\Put(
     *     path="/api/pages/{id}",
     *     description="Update a ApiPage",
     *     operationId="putApiPage",
     *     produces={"application/json"},
     *     tags={"pages"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         description="The node translation ID",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="apiPage",
     *         in="body",
     *         type="object",
     *         @SWG\Schema(ref="#/definitions/ApiPage")
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="Returned when successful",
     *         @SWG\Schema(ref="#/definitions/ApiPage")
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
     * @ParamConverter(
     *     name="apiPage",
     *     converter="fos_rest.request_body",
     *     class="Kunstmaan\Rest\NodeBundle\Model\ApiPage",
     *     options={
     *          "deserializationContext"={
     *              "groups"={
     *                  "edit"
     *              }
     *          },
     *          "validator"={
     *              "groups"={
     *                  "edit",
     *                  "Default"
     *              }
     *          }
     *     }
     * )
     *
     * @param Request                          $request
     * @param ApiPage                          $apiPage
     * @param integer                          $id
     * @param ConstraintViolationListInterface $validationErrors
     */
    public function putPagesAction(Request $request, ApiPage $apiPage, $id, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            return new \FOS\RestBundle\View\View($validationErrors, Response::HTTP_BAD_REQUEST);
        }

        if (null === ($nodeTranslation = $apiPage->getNodeTranslation())) {
            $nodeTranslation = $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')->find($id);
        }

        $node = $apiPage->getNode();
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();

        list($nodeVersionIsLocked, $nodeVersion) = $this->nodeHelper->createNodeVersion($nodeTranslation, $nodeVersion);

        $page = $nodeVersion->getRef($this->em);
        $isStructureNode = $page->isStructureNode();

        $this->pagePartHelper->updatePageParts($apiPage, $page);
        $this->nodeHelper->updateNode($node, $nodeTranslation, $nodeVersion, $page, $isStructureNode);
    }

    /**
     * Creates a ApiPage
     *
     * @View(
     *     statusCode=204
     * )
     *
     * @Rest\Route(path="/pages")
     *
     * @SWG\Post(
     *     path="/api/pages",
     *     description="Creates a ApiPage",
     *     operationId="postApiPage",
     *     produces={"application/json"},
     *     tags={"pages"},
     *     @SWG\Parameter(
     *         name="apiPage",
     *         in="body",
     *         type="object",
     *         @SWG\Schema(ref="#/definitions/PostApiPage"),
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="Returned when successful",
     *         @SWG\Schema(ref="#/definitions/ApiPage")
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
     * @ParamConverter(
     *     name="apiPage",
     *     converter="fos_rest.request_body",
     *     class="Kunstmaan\Rest\NodeBundle\Model\ApiPage",
     *     options={
     *          "deserializationContext"={
     *              "groups"={
     *                  "create"
     *              }
     *          },
     *          "validator"={
     *              "groups"={
     *                  "create",
     *                  "Default"
     *              }
     *          }
     *     }
     * )
     *
     * @param Request                          $request
     * @param ApiPage                          $apiPage
     * @param ConstraintViolationListInterface $validationErrors
     */
    public function postPagesAction(Request $request, ApiPage $apiPage, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            return new \FOS\RestBundle\View\View($validationErrors, Response::HTTP_BAD_REQUEST);
        }

        /* @var Node $parentNode */
        $parentNode = $apiPage->getNode()->getParent();
        $locale = $apiPage->getNodeTranslation()->getLang();

        $this->nodeHelper->createNodeTranslation($parentNode);
    }
}
