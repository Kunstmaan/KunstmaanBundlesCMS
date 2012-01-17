<?php

namespace Kunstmaan\SearchBundle\Transformers;

use Elastica_Document;
use RuntimeException;
use FOQ\ElasticaBundle\Transformer\ModelToElasticaAutoTransformer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Kunstmaan\SearchBundle\Entity\Indexable;


class NodeTransformer extends ModelToElasticaAutoTransformer
{
    // Contains the Symfony2 DependencyInjection container
    protected $container;


    public function __construct(array $options = array())
    {
        $this->container = $options['container'];

        parent::__construct($options);
    }

    /**
     * Transforms a given object into an instance of an Elastica_Document
     *
     * @param $object
     * @param array $fields
     * @return \Elastica_Document
     */
    public function transform($object, array $fields)
    {
        $customMappings = $this->container->getParameter('kunstmaan_search.website_custom_mappings');
        $array = array();

        foreach($fields as $field) {
            if( isset($customMappings[$field]) && (isset($customMappings[$field]['handlerclass']) && isset($customMappings[$field]['handlermethod']) )) {
                $array[$field] = $this->getHandlerField($this->container, $object, $field, $customMappings[$field]);
            } else {
                $array[$field] = $this->getNormalField($this->container, $object, $field);
            }
        }

        //gets the identifier field, most of the time this will be the id field
        // if the identifier field is already fetched, use that, don't recompute
        if(isset($array[$this->options['identifier']])) {
            //the identifier field was empty, so use either the normal or handler method
            if( isset($customMappings[$field]) && (isset($customMappings[$this->options['identifier']]['handlerclass']) && isset($customMappings[$this->options['identifier']]['handlermethod']) )) {
                $identifier = $this->getHandlerField($this->container, $object, $field, $customMappings[$field]);
            } else {
                $identifier = $this->getNormalField($this->container, $object, $this->options['identifier']);
            }
        } else {
            $identifier = $array[$this->options['identifier']];
        }

        return new Elastica_Document($identifier, array_filter($array));
    }

    /**
     * Handles the 'normal' parameters via the getter method of the entity
     *
     * @param $container
     * @param $object
     * @param $field
     * @return array|string
     * @throws RuntimeException
     */
    protected function getNormalField($container, $object, $field)
    {
        $class = get_class($object);

        $getter = 'get'.ucfirst($field);
        if (!method_exists($class, $getter)) {
            throw new RuntimeException(sprintf('The getter %s::%s does not exist', $class, $getter));
        }

        return $this->normalizeValue($object->$getter());
    }

    /**
     * Handles the special way of getting the data. Will instanciate a given class and call a method to receive
     * output.
     *
     * @param $container
     * @param $object
     * @param $field
     * @param $mappingSettings
     * @return mixed
     */
    protected function getHandlerField($container, $object, $field, $mappingSettings)
    {
        //basic checks
        if(!class_exists($mappingSettings['handlerclass'])) {
            throw new RuntimeException(sprintf('The handlerclass %s does not exist', $mappingSettings['handlerclass']));
        }

        if (!method_exists($mappingSettings['handlerclass'], $mappingSettings['handlermethod'])) {
            throw new RuntimeException(sprintf('The handlermethod %s::%s does not exist', $mappingSettings['handlerclass'], $mappingSettings['handlermethod']));
        }

        //instanciate the class, call the method and return the output
        $class = new $mappingSettings['handlerclass']();
        $searchable = $class->$mappingSettings['handlermethod']($container, $object, $field);

        //gets the output from the getContentForIndexing method, but only if it's an instance of Indexable
        $output = '';
        if($searchable instanceof Indexable) {
            $output = $searchable->getContentForIndexing($container, $object);
        }

        return $output;
    }
}