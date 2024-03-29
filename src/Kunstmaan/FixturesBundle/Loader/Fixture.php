<?php

namespace Kunstmaan\FixturesBundle\Loader;

class Fixture
{
    private $name;

    private $class;

    private $spec;

    private $entity;

    private $additionalEntities;

    private $properties;

    private $parameters;

    private $translations;

    public function __construct($name, $class, $specs)
    {
        $this->additionalEntities = [];
        $this->properties = [];
        $this->parameters = [];
        $this->translations = [];

        $this->setName($name);
        $this->setClass($class);

        foreach ($specs as $spec => $data) {
            if ($spec != 'translations' && $spec != 'parameters') {
                $this->addProperty($spec, $data);
            } elseif ($spec == 'translations') {
                $this->setTranslations($data);
            } elseif ($spec == 'parameters') {
                $this->setParameters($data);
            }
        }
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Fixture
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return Fixture
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function addProperty($name, $value)
    {
        $this->properties[$name] = $value;
    }

    /**
     * @return Fixture
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;

        return $this;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return Fixture
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @return Fixture
     */
    public function setTranslations($translations)
    {
        $this->translations = $translations;

        return $this;
    }

    /**
     * @return array
     */
    public function getAdditionalEntities()
    {
        return $this->additionalEntities;
    }

    /**
     * @param array $additionalEntities
     *
     * @return Fixture
     */
    public function setAdditionalEntities($additionalEntities)
    {
        $this->additionalEntities = $additionalEntities;

        return $this;
    }

    public function addAdditional($name, $value)
    {
        $this->additionalEntities[$name] = $value;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return Fixture
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    public function getSpec()
    {
        return $this->spec;
    }

    /**
     * @return Fixture
     */
    public function setSpec($spec)
    {
        $this->spec = $spec;

        return $this;
    }
}
