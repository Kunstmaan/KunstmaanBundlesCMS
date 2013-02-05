<?php
namespace Kunstmaan\SearchBundle\Transformers;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Kunstmaan\SearchBundle\Entity\IndexableInterface;
use Elastica_Document;
use RuntimeException;
use FOQ\ElasticaBundle\Transformer\ModelToElasticaAutoTransformer;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

/**
 * NodeTransformer
 */
class NodeTransformer extends ModelToElasticaAutoTransformer
{
    // Contains the Symfony2 DependencyInjection container
    protected $container;

    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->container = $options['container'];
        parent::__construct($options);
    }

    /**
     * Transforms a given object into an instance of an Elastica_Document
     *
     * @param mixed $object The object
     * @param array $fields The fields
     *
     * @return \Elastica_Document
     */
    public function transform($object, array $fields)
    {
        $customMappings = $this->container->getParameter('kunstmaan_search.website_custom_mappings');
        $array = array();
        foreach ($fields as $key => $value) {
            if (isset($customMappings[$key]) && (isset($customMappings[$key]['handlerclass']) && isset($customMappings[$key]['handlermethod']))) {
                $array[$key] = $this->getHandlerField($this->container, $object, $key, $customMappings[$key]);
            } else {
                $array[$key] = $this->getNormalField($this->container, $object, $key);
            }
        }

        var_dump($array);

        //gets the identifier field, most of the time this will be the id field
        // if the identifier field is already fetched, use that, don't recompute
        if (isset($array[$this->options['identifier']])) {
            //the identifier field was empty, so use either the normal or handler method
            if (isset($customMappings[$key]) && (isset($customMappings[$this->options['identifier']]['handlerclass']) && isset($customMappings[$this->options['identifier']]['handlermethod']))) {
                $identifier = $this->getHandlerField($this->container, $object, $key, $customMappings[$key]);
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
     * @param ContainerInterface $container The container
     * @param mixed              $object    The object
     * @param string             $field     The field
     *
     * @return array|string
     * @throws RuntimeException
     */
    protected function getNormalField(ContainerInterface $container, $object, $field_name)
    {
        $class = ClassLookup::getClass($object);
        $getter = 'get' . ucfirst($field_name);
        if (!method_exists($class, $getter)) {
            var_dump($field_name);
            throw new RuntimeException(sprintf('The getter %s::%s does not exist', $class, $getter));
        }

        return $this->normalizeValue($object->$getter());
    }

    /**
     * Handles the special way of getting the data. Will instanciate a given class and call a method to receive
     * output.
     *
     * @param ContainerInterface $container       The container
     * @param mixed              $object          The object
     * @param string             $field           The field
     * @param array              $mappingSettings The mapping settings
     *
     * @throws \RuntimeException
     * @return mixed
     */
    protected function getHandlerField(ContainerInterface $container, $object, $field_name, array $mappingSettings)
    {
        //basic checks
        if (!class_exists($mappingSettings['handlerclass'])) {
            throw new RuntimeException(sprintf('The handlerclass %s does not exist', $mappingSettings['handlerclass']));
        }
        if (!method_exists($mappingSettings['handlerclass'], $mappingSettings['handlermethod'])) {
            throw new RuntimeException(sprintf('The handlermethod %s::%s does not exist', $mappingSettings['handlerclass'], $mappingSettings['handlermethod']));
        }
        //instanciate the class, call the method and return the output
        $class = new $mappingSettings['handlerclass']();
        $searchResult = $class->$mappingSettings['handlermethod']($container, $object, $field_name);
        $output = '';
        //gets the output from the getContentForIndexing method, but only if it's an instance of IndexableInterface
        if ($searchResult instanceof IndexableInterface) {
            var_dump('found an indexableinterface');
            /** @var IndexableInterface $seachresult */
            $output = $searchResult->getContentForIndexing($container, $object);
            var_dump($output);
        } else {
            $output = $searchResult;
        }

        return $output;
    }
}
