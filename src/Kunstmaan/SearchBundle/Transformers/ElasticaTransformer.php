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
     * @param ObjectManager $objectManager The object manager
     * @param string        $objectClass   The class
     * @param array         $options       Options
     */
    public function __construct(ObjectManager $manager, $objectClass, array $options = array())
    {
        var_dump('before ElasticaTransformer parent constructor');
        //parent::__construct($manager, $objectClass, array('identifier' => $this->options['identifier'], 'hydrate' => $this->options['hydrate']));
        //$this->options = array_merge($this->options, $options);
        var_dump('after ElasticaTransformer parent constructor');

        $this->registry = $manager;
        $this->objectClass = $objectClass;
        // TODO: Options are in a different arrayformat than what this class expects. So it's using the defaults.
        $this->options = array_merge($this->options, $options);
        var_dump($this->options);
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
        var_dump('start transform');
        var_dump($elasticaObjects);
        $ids = array_map(function($elasticaObject) {
            return $elasticaObject->getId();
        }, $elasticaObjects);

        var_dump("ids::::");
        var_dump($ids);

        // This returns 'regular' NodeTranslation objects.
        $objects = $this->findByIdentifiers($ids, $this->options['hydrate']);
        return $objects;

        // TODO: Order by ID. Why not relevance like ElasticSearch returns?
        // TODO: Highlight results.

        /*
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
        */
    }
}
