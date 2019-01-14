<?php

namespace Kunstmaan\AdminBundle\Composer;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Parser;

class ScriptHandler
{
    protected static $options = array(
        'symfony-app-dir' => 'app',
        'multi-language-option' => 'multilanguage',
    );

    /**
     * @param Event $event
     */
    public static function checkMultiLangConfigs(Event $event)
    {
        $options = static::getOptions($event);
        $appDir = getcwd() . '/' . $options['symfony-app-dir'];
        $configDir = $appDir . '/config';
        $parametersFile = $configDir . '/parameters.yml';
        $routingFile = $configDir . '/routing.yml';
        $securityFile = $configDir . '/security.yml';
        $singleLangRoutingFile = $configDir . '/routing.singlelang.yml';
        $singleLangSecurityFile = $configDir . '/security.singlelang.yml';
        $multiLangRoutingFile = $configDir . '/routing.multilang.yml';
        $multiLangSecurityFile = $configDir . '/security.multilang.yml';

        if (is_file($parametersFile)) {
            $parameters = self::getConfigParameters($parametersFile);

            if (isset($parameters[$options['multi-language-option']])) {
                $multiLanguage = $parameters[$options['multi-language-option']];
                if (!$multiLanguage) {
                    $fs = new Filesystem();

                    // move routing
                    if (is_file($singleLangRoutingFile) && !is_file($multiLangRoutingFile)) {
                        try {
                            $fs->rename($routingFile, $multiLangRoutingFile);
                            $fs->rename($singleLangRoutingFile, $routingFile);
                            $event->getIO()->write(sprintf('Replaced routing config with single language config'));
                        } catch (IOException $ioE) {
                            $event->getIO()->write(sprintf('Exception while moving routing file to singlelang routing file: <error>%s</error>', $ioE->getMessage()));
                        }
                    }
                    // move security
                    if (is_file($singleLangSecurityFile) && !is_file($multiLangSecurityFile)) {
                        try {
                            $fs->rename($securityFile, $multiLangSecurityFile);
                            $fs->rename($singleLangSecurityFile, $securityFile);
                            $event->getIO()->write(sprintf('Replaced security config with single language config'));
                        } catch (IOException $ioE) {
                            $event->getIO()->write(sprintf('Exception while moving routing file to singlelang routing file: <error>%s</error>', $ioE->getMessage()));
                        }
                    }
                }
            }
        }
    }

    /**
     * @param Event $event
     *
     * @return array
     */
    protected static function getOptions(Event $event)
    {
        return array_merge(static::$options, $event->getComposer()->getPackage()->getExtra());
    }

    /**
     * @param string $parametersFile
     *
     * @return array
     */
    protected static function getConfigParameters($parametersFile)
    {
        $ymlParser = new Parser();
        $config = $ymlParser->parse(file_get_contents($parametersFile));

        return isset($config['parameters']) ? $config['parameters'] : array();
    }
}
