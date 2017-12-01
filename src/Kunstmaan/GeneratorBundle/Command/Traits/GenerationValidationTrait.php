<?php

namespace Kunstmaan\GeneratorBundle\Command\Traits;

use InvalidArgumentException;

/**
 * Trait GenerationValidationTrait
 * @package Kunstmaan\GeneratorBundle\Command\Traits
 */
trait GenerationValidationTrait
{
    /**
     * @param $question
     * @param $generator
     * @param $bundlePath
     * @return mixed
     */
    public function getNameByValidationClosure($question, $generator, $bundlePath)
    {
        $name = $this->assistant->askAndValidate(
            $question,
            function ($name) use ($generator, $bundlePath) {
                // Check reserved words
                if ($generator->isReservedKeyword($name)){
                    throw new InvalidArgumentException(sprintf('"%s" is a reserved word', $name));
                }

                // Name should end on Page
                if (!preg_match('/Page$/', $name)) {
                    throw new InvalidArgumentException('The page name must end with Page');
                }

                // Name should contain more characters than Page
                if (strlen($name) <= strlen('Page') || !preg_match('/^[a-zA-Z]+$/', $name)) {
                    throw new InvalidArgumentException('Invalid page name');
                }

                // Check that entity does not already exist
                if (file_exists($bundlePath . '/Entity/Pages/' . $name . '.php')) {
                    throw new InvalidArgumentException(sprintf('Page or entity "%s" already exists', $name));
                }

                return $name;
            }
        );
        return $name;
    }
}

