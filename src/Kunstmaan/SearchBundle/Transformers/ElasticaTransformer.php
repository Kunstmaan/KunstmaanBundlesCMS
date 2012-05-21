<?php
namespace Kunstmaan\SearchBundle\Transformers;

use FOQ\ElasticaBundle\Doctrine\ORM\ElasticaToModelTransformer;

class ElasticaTransformer extends ElasticaToModelTransformer
{

    public function __construct($objectManager, $objectClass, array $options = array())
    {
        $this->objectManager = $objectManager;
        $this->objectClass = $objectClass;
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Transforms an array of elastica objects into an array of
     * model objects fetched from the doctrine repository
     *
     * @param array $elasticaObjects an array of elastica objects
     * @return array
     **/
    public function transform(array $elasticaObjects)
    {
        $ids = array_map(function($elasticaObject) {
            return $elasticaObject->getId();
        }, $elasticaObjects);
        $objects = $this->findByIdentifiers($this->objectClass, $this->options['identifier'], $ids, $this->options['hydrate']);
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
