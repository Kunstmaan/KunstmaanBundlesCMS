<?php

namespace Kunstmaan\FixturesBundle\Parser\Property;

class Method implements PropertyParserInterface
{
    const REGEX = '/<[a-zA-Z0-9]+\([^\)]*\)>/';

    /**
     * Check if this parser is applicable
     *
     * @return bool
     */
    public function canParse($value)
    {
        if (is_string($value) && preg_match(self::REGEX, $value)) {
            return true;
        }

        return false;
    }

    /**
     * Parse provided value into new data
     *
     * @param $value
     * @param $providers
     * @param array $references
     * @param array $additional
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function parse($value, $providers, $references = [], $additional = [])
    {
        preg_match_all(self::REGEX, $value, $matches);

        foreach ($matches[0] as $pattern) {
            preg_match_all('/[^,\(\)<>]+/', $pattern, $arguments);
            $arguments = $arguments[0];
            $method = array_shift($arguments);
            $arguments = array_map(function ($arg) {
                return trim(trim($arg), '\'""');
            }, $arguments);

            foreach ($providers as $provider) {
                /*
                 * Call method from provider with/without arguments
                 * 1: Arguments are passed through from fixture
                 * 2: Search if method needs arguments en find them through typehint and additional params
                 * 3: Magic methods without arguments
                 */
                if (method_exists($provider, $method)) {
                    $refl = new \ReflectionMethod($provider, $method);

                    if (count($arguments) < $refl->getNumberOfRequiredParameters()) {
                        $parameters = $refl->getParameters();
                        $parametersNeeded = array_slice($parameters, count($arguments));
                        $arguments = array_merge($arguments, $this->findArguments($parametersNeeded, $additional));

                        if (count($parameters) !== count($arguments)) {
                            throw new \Exception('Can not match all arguments for provider ' . get_class($provider));
                        }
                    }

                    $value = $this->processValue($pattern, $refl->invokeArgs($provider, $arguments), $value, $matches[0]);

                    break;
                } elseif (is_callable([$provider, $method])) {
                    $value = $this->processValue($pattern, call_user_func_array(array($provider, $method), $arguments), $value, $matches[0]);

                    break;
                }
            }
        }

        return $value;
    }

    private function processValue($pattern, $result, $value, $patterns)
    {
        if (!is_string($result) && !is_int($result) && count($patterns) > 1 && strlen(str_replace($pattern, '', $value)) > 0) {
            throw new \Exception(sprintf('Collision during processing of pattern "%s" on value "%s"', $pattern, $value));
        }

        if (!is_string($result) && !is_int($result)) {
            return $result;
        }

        return str_replace($pattern, $result, $value);
    }

    /**
     * @param $parameters
     * @param $additional
     *
     * @return array
     */
    private function findArguments($parameters, $additional)
    {
        $arguments = [];
        if (count($parameters) == 0) {
            return $arguments;
        }

        foreach ($parameters as $parameter) {
            $argument = $this->typeHintChecker($parameter, $additional);
            if ($argument !== null) {
                $arguments[] = $argument;

                continue;
            }

            $argument = $this->getArgumentByName($parameter, $additional);
            if ($argument !== null) {
                $arguments[] = $argument;
            }
        }

        return $arguments;
    }

    /**
     * @param \ReflectionParameter $parameter
     * @param $parameters
     *
     * @return null|object
     */
    private function typeHintChecker(\ReflectionParameter $parameter, $parameters)
    {
        $class = $parameter->getClass();
        $typeHint = null;
        if (!$class instanceof \ReflectionClass) {
            return null;
        }

        $typeHint = $class->getName();
        foreach ($parameters as $item) {
            if ($item instanceof $typeHint) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param \ReflectionParameter $parameter
     * @param $parameters
     *
     * @return null|mixed
     */
    private function getArgumentByName(\ReflectionParameter $parameter, $parameters)
    {
        foreach ($parameters as $name => $item) {
            $paramName = $parameter->getName();
            if ($name === $paramName) {
                return $item;
            }
        }

        return null;
    }
}
