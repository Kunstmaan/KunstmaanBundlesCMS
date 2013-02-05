<?php
namespace Kunstmaan\SearchBundle\Transformers;

use Doctrine\Common\Persistence\ObjectManager;

use FOQ\ElasticaBundle\Doctrine\ORM\ElasticaToModelTransformer;

/**
 * ElasticaTransformer
 */
class ElasticaTransformer extends ElasticaToModelTransformer
{

    /**
     * @param Registry      $registry      The doctrine registry
     * @param string        $objectClass   The class
     * @param array         $options       Options
     */
    public function __construct($registry, $objectClass, array $options = array())
    {
        $this->registry = $registry;
        $this->objectClass = $objectClass;
        $this->options = array_merge($this->options, $this->fromArrayToHashArray($options));
    }

    private function fromArrayToHashArray(array $array)
    {
      $hash_array = array();

      foreach ($array as $key => $value)
      {
          if (count($value[key($value)]) == 1)
          {
              $hash_array[key($value)] = $value[key($value)];
          }
      }

      return $hash_array;
    }

    /**
     * Transforms an array of elastica objects into an array of
     * model objects fetched from the doctrine repository
     *
     * @param array $elasticaObjects an array of elastica objects
     *
     * @return array
     **/
    public function transform(array $elasticaObjects)
    {
        $ids = array_map(function($elasticaObject) {
            return $elasticaObject->getId();
        }, $elasticaObjects);

        // This returns 'regular' NodeTranslation objects.
        $objects = $this->findByIdentifiers($ids, $this->options['hydrate']);

        $identifierGetter = 'get' . ucfirst($this->options['identifier']);
        // sort objects in the order of ids
        $idPos = array_flip($ids);
        usort($objects, function($a, $b) use ($idPos, $identifierGetter) {
            return $idPos[$a->$identifierGetter()] > $idPos[$b->$identifierGetter()];
        });

        foreach ($objects as &$object) {
            foreach ($elasticaObjects as $elasticaObject) {
                $id = $elasticaObject->getParam('_id');
                if ($object->getId() == $id) {
                    $object->highlight = $elasticaObject->getParam('highlight');
                    break;
                }
            }
        }

        return $objects;
    }
}
