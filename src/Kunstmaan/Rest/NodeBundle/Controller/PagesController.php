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
use Kunstmaan\Rest\NodeBundle\Service\DataTransformerService;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class PagesController
 *
 * @author Ruud Denivel <ruud.denivel@kunstmaan.be>
 *
 * @Route(service="kunstmaan_api.controller.pages")
 */
class PagesController
{
    use ControllerTrait;

    /** @var EntityManagerInterface */
    private $em;

    /** @var DataTransformerService */
    private $dataTransformer;

    public function __construct(EntityManagerInterface $em, DataTransformerService $dataTransformer)
    {
        $this->em = $em;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * Retrieve nodes paginated
     *
     * ApiDoc(
     *  resource=true,
     *  description="Get a page",
     *  resourceDescription="Get a page",
     *  parameters={
     *      {
     *          "name"="type",
     *          "dataType"="string",
     *          "requirement"="\s+",
     *          "required"=true,
     *          "description"="The FQCN of the page"
     *      },
     *      {
     *          "name"="locale",
     *          "dataType"="string",
     *          "required"=true,
     *          "requirement"="\s+",
     *          "description"="The language of your content"
     *      },
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when the there are missing required parameters",
     *      403="Returned when the user is not authorized to fetch nodes",
     *      404="Returned when nothing was found for given ID",
     *      500="Something went wrong"
     *  }
     * )
     *
     * @SWG\Get(
     *     path="/api/pages/{id}",
     *     description="Get a page of a certain type by ID",
     *     operationId="getPages",
     *     produces={"application/json"},
     *     tags={"pages"},
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
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     ),
     *     @SWG\Response(
     *         response="default",
     *         description="unexpected error",
     *         @SWG\Schema(ref="#/definitions/ErrorModel")
     *     )
     * )
     */
    public function getPagesAction(Request $request, $id)
    {
        if (!$request->query->has('locale')) {
            throw new HttpException(400, "Missing locale");
        }

        $locale = $request->getLocale();

        $qb = $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')->getOnlineNodeTranslationsQueryBuilder($locale);
        $qb
            ->andWhere('n.id = :nodeId')
            ->setParameter('nodeId', $id)
        ;

        $nodeTranslation = $qb->getQuery()->getOneOrNullResult();
        if (!$nodeTranslation instanceof NodeTranslation) {
            throw new NotFoundHttpException();
        }

        $data = $this->dataTransformer->transform($nodeTranslation);

        return $this->handleView($this->view($data, Response::HTTP_OK));
    }
}