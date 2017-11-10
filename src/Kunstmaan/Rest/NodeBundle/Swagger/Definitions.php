<?php

/*
 * This file is part of the KunstmaanBundlesCMS package.
 *
 * (c) Kunstmaan <https://github.com/Kunstmaan/KunstmaanBundlesCMS/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kunstmaan\Rest\NodeBundle\Swagger;

/**
 * Class Definitions
 * @package Kunstmaan\Rest\NodeBundle
 *
 * @SWG\Definition(
 *   definition="Node",
 *   type="object",
 *   properties={
 *          @SWG\Property(property="id",type="integer", example=1),
 *          @SWG\Property(
 *              property="nodetranslations",
 *              type="array",
 *              @SWG\Items(
 *                  allOf={
 *                      @SWG\Schema(ref="#/definitions/NodeTranslation")
 *                  }
 *              )
 *          ),
 *          @SWG\Property(property="hidden_from_nav",type="boolean"),
 *          @SWG\Property(property="ref_entity_name",type="string", example="Kunstmaan\SomeBundle\Entity\Pages\HomePage"),
 *          @SWG\Property(property="internal_name",type="string", example="homepage"),
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="NodeTranslation",
 *   type="object",
 *   properties={
 *          @SWG\Property(property="id",type="integer", example=1),
 *          @SWG\Property(property="lang",type="string", example="nl"),
 *          @SWG\Property(property="online",type="boolean"),
 *          @SWG\Property(property="title",type="string"),
 *          @SWG\Property(property="public_node_version", ref="#/definitions/NodeVersion"),
 *          @SWG\Property(property="weight",type="integer"),
 *   }
 * )
 *
 * @SWG\Definition(
 *   definition="NodeVersion",
 *   type="object",
 *   properties={
 *          @SWG\Property(property="id",type="integer", example=1),
 *          @SWG\Property(property="owner",type="string", example="admin"),
 *          @SWG\Property(property="ref_id",type="boolean"),
 *          @SWG\Property(property="ref_entity_name",type="string", example="Kunstmaan\SomeBundle\Entity\Pages\HomePage"),
 *   }
 * )
 *
 */
class Definitions
{

}