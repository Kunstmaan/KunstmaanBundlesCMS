<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Helper\CommandAssistant;
use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCommand;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

abstract class KunstmaanGenerateCommand extends GenerateDoctrineCommand
{
    /**
     * @var CommandAssistant
     */
    protected $assistant;

    /**
     * Interacts with the user.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->setInputAndOutput($input, $output);

        $this->assistant->writeSection($this->getWelcomeText());

        $this->doInteract();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \RuntimeException
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setInputAndOutput($input, $output);

        return $this->doExecute();
    }

    /**
     * Create the CommandAssistant.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function setInputAndOutput(InputInterface $input, OutputInterface $output)
    {
        if (is_null($this->assistant)) {
            $this->assistant = new CommandAssistant();
            $this->assistant->setDialog($this->getDialogHelper());
            $this->assistant->setKernel($this->getApplication()->getKernel());
        }
        $this->assistant->setOutput($output);
        $this->assistant->setInput($input);
    }

    /**
     * Do the interaction with the end user.
     */
    abstract protected function doInteract();

    /**
     * This function implements the final execution of the Generator.
     * It calls the execute function with the correct parameters.
     */
    protected abstract function doExecute();

    /**
     * The text to be displayed on top of the generator.
     *
     * @return string|array
     */
    protected abstract function getWelcomeText();

    /**
     * Get an array with all the bundles the user has created.
     *
     * @return array
     */
    protected function getOwnBundles()
    {
        $bundles = array();
        $counter = 1;

        $dir = dirname($this->getContainer()->getParameter('kernel.root_dir')).'/src/';
        $files = scandir($dir);
        foreach ($files as $file) {
            if (is_dir($dir.$file) && !in_array($file, array('.', '..'))) {
                $bundleFiles = scandir($dir.$file);
                foreach ($bundleFiles as $bundleFile) {
                    if (is_dir($dir.$file.'/'.$bundleFile) && !in_array($bundleFile, array('.', '..'))) {
                        $bundles[$counter++] = array(
                            'name' => $bundleFile,
                            'namespace' => $file,
                            'dir' => $dir.$file.'/'.$bundleFile
                        );
                    }
                }
            }
        }

        return $bundles;
    }

    /**
     * Check that a bundle is available (loaded in AppKernel)
     *
     * @param string $bundleName
     * @return bool
     */
    protected function isBundleAvailable($bundleName)
    {
        $allBundles = array_keys($this->assistant->getKernel()->getBundles());
        return in_array($bundleName, $allBundles);
    }

    /**
     * Asks for the prefix and sets it on the InputInterface as the 'prefix' option, if this option is not set yet.
     * Will set the default to a snake_cased namespace when the namespace has been set on the InputInterface.
     *
     * @param array  $text What you want printed before the prefix is asked. If null is provided it'll write a default text.
     * @param string $namespace An optional namespace. If this is set it'll create the default based on this prefix.
     *  If it's not provided it'll check if the InputInterface already has the namespace option.
     *
     * @return string The prefix. But it's also been set on the InputInterface.
     */
    protected function askForPrefix(array $text = null, $namespace = null)
    {
        $prefix = $this->assistant->getOptionOrDefault('prefix', null);

        if (is_null($text)) {
            $text = array(
                '',
                'You can add a prefix to the table names of the generated entities for example: '.
                '<comment>projectname_bundlename_</comment>',
                'Enter an underscore \'_\' if you don\'t want a prefix.',
                ''
            );
        }

        if (is_null($prefix)) {
            if (count($text) > 0) {
                $this->assistant->writeLine($text);
            }

            if (is_null($namespace) || empty($namespace)) {
                $namespace = $this->assistant->getOption('namespace');
            } else {
                $namespace = $this->fixNamespace($namespace);
            }
            $defaultPrefix = GeneratorUtils::cleanPrefix($this->convertNamespaceToSnakeCase($namespace));
            $prefix = GeneratorUtils::cleanPrefix($this->assistant->ask('Tablename prefix', $defaultPrefix));
            $this->assistant->setOption('prefix', $prefix);
        }

        return $prefix;
    }

    /**
     * Converts something like Namespace\BundleNameBundle to namspace_bundlenamebundle.
     *
     * @param string $namespace
     * @return string
     */
    private function convertNamespaceToSnakeCase($namespace)
    {
        if (is_null($namespace)) {
            return null;
        }

        return str_replace('/', '_', strtolower($this->fixNamespace($namespace)));
    }

    /**
     * Replaces '\' with '/'.
     *
     * @param $namespace
     * @return mixed
     */
    private function fixNamespace($namespace)
    {
        return str_replace('\\', '/', $namespace);
    }

    /**
     * Ask for which bundle we need to generate something. It there is only one custome bundle
     * created by the user, we don't ask anything and just use that bundle.
     *
     * @param string $objectName
     * @return BundleInterface
     */
    protected function askForBundleName($objectName)
    {
        $ownBundles = $this->getOwnBundles();
        if (count($ownBundles) <= 0) {
            $this->assistant->writeError("Looks like you don't have created any bundles for your project...", true);
        }

        // If we only have 1 bundle, we don't need to ask
        if (count($ownBundles) > 1) {
            $bundleSelect = array();
            foreach ($ownBundles as $key => $bundleInfo) {
                $bundleSelect[$key] = $bundleInfo['namespace'].':'.$bundleInfo['name'];
            }
            $bundleId = $this->assistant->askSelect('In which bundle do you want to create the '.$objectName, $bundleSelect);
            $bundleName = $ownBundles[$bundleId]['namespace'].$ownBundles[$bundleId]['name'];

            $this->assistant->writeLine('');
        } else {
            $bundleName = $ownBundles[1]['namespace'].$ownBundles[1]['name'];
            $this->assistant->writeLine(array("The $objectName will be created for the <comment>$bundleName</comment> bundle.\n"));
        }
        $bundle = $this->assistant->getKernel()->getBundle($bundleName);

        return $bundle;
    }

    /**
     * Ask the end user to select one (or more) section configuration(s).
     *
     * @param string $question
     * @param BundleInterface $bundle
     * @param bool $multiple
     * @param string|null $context
     * @return array
     */
    protected function askForSections($question, BundleInterface $bundle, $multiple = false, $context = null)
    {
        $allSections = $this->getAvailableSections($bundle, $context);
        $sections = array();

        if (count($allSections) > 0) {
            $sectionSelect = array();
            foreach ($allSections as $key => $sectionInfo) {
                $sectionSelect[$key] = $sectionInfo['name'].' ('.$sectionInfo['file'].')';
            }
            $this->assistant->writeLine('');
            $sectionIds = $this->assistant->askSelect($question, $sectionSelect, null, $multiple);
            foreach ($sectionIds as $id) {
                $sections[] = $allSections[$id]['file'];
            }
        }

        return $sections;
    }

    /**
     * Get an array with the available page sections. We also parse the yaml files to get more information about
     * the sections.
     *
     * @param BundleInterface $bundle  The bundle for which we want to get the section configuration
     * @param string|null     $context If provided, only return configurations with this context
     * @return array
     */
    protected function getAvailableSections(BundleInterface $bundle, $context = null) {
        $configs = array();
        $counter = 1;

        $dir = $bundle->getPath().'/Resources/config/pageparts/';
        if (file_exists($dir) && is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if (is_file($dir.$file) && !in_array($file, array('.', '..')) && substr($file, -4) == '.yml') {
                    $info = $this->getSectionInfo($dir, $file);
                    if (is_array($info) && (is_null($context) || $info['context'] == $context)) {
                        $configs[$counter++] = $info;
                    }
                }
            }
        }

        return $configs;
    }

    /**
     * Get the information about a pagepart section configuration file.
     *
     * @param string $dir
     * @param string$file
     * @return array|null
     */
    private function getSectionInfo($dir, $file)
    {
        $info = null;
        try {
            $data = Yaml::parse($dir.$file);
            $info = array(
                'name' => $data['name'],
                'context' => $data['name'],
                'file' => $file,
                //'file_clean' => substr($file, 0, strlen($file)-4)
            );
        } catch (ParseException $e) {}

        return $info;
    }
}