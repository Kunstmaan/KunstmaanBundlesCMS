<?php

namespace Kunstmaan\GeneratorBundle\Helper;

use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @deprecated the functions in this class should be moved to the KunstmaanGenerateCommand class.
 */
class InputAssistant
{
    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var DialogHelper */
    private $dialog;

    /** @var Kernel */
    private $kernel;

    /** @var ContainerInterface */
    private $container;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param DialogHelper $dialog
     * @param Kernel $kernel
     * @param ContainerInterface $container
     */
    public function __construct(InputInterface &$input, OutputInterface $output, DialogHelper $dialog, Kernel $kernel, ContainerInterface $container)
    {
        $this->input = $input;
        $this->output = $output;
        $this->dialog = $dialog;
        $this->kernel = $kernel;
        $this->container = $container;
    }

    /**
     * Asks for the namespace and sets it on the InputInterface as the 'namespace' option, if this option is not set yet.
     *
     * @param array $text What you want printed before the namespace is asked.
     *
     * @return string The namespace. But it's also been set on the InputInterface.
     */
    public function askForNamespace(array $text = null)
    {
        $namespace = $this->input->hasOption('namespace') ? $this->input->getOption('namespace') : null;

        // When the Namespace is filled in return it immediately if valid.
        try {
            if (!is_null($namespace) && !empty($namespace)) {
                Validators::validateBundleNamespace($namespace);
                return $namespace;
            }
        } catch (\Exception $error) {
            $this->writeError(array("Namespace '$namespace' is incorrect. Please provide a correct value.", $error->getMessage()));
            exit;
        }

        $ownBundles = $this->getOwnBundles();
        if (count($ownBundles) <= 0) {
            $this->writeError("Looks like you don't have created a bundle for your project, create one first.", true);
        }

        $namespace = '';

        // If we only have 1 or more bundles, we can prefill it.
        if (count($ownBundles) > 0) {
            $namespace = $ownBundles[1]['namespace'] . '/' . $ownBundles[1]['name'];
        }


        $namespaces = $this->getNamespaceAutoComplete($this->kernel);

        if (!is_null($text) && (count($text) > 0)) {
            $this->output->writeln($text);
        }

        $namespace = $this->dialog->askAndValidate($this->output, $this->dialog->getQuestion('Bundle Namespace', $namespace), array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateBundleNamespace'), false, $namespace, $namespaces);

        if ($this->input->hasOption('namespace')) {
            $this->input->setOption('namespace', $namespace);
        }

        return $namespace;
    }

    /**
     * Helper function to display errors in the console.
     *
     * @param $message
     * @param bool $exit
     */
    private function writeError($message, $exit = false)
    {
        $this->output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($message, 'error'));
        if ($exit) {
            exit;
        }
    }

    /**
     * Get an array with all the bundles the user has created.
     *
     * @return array
     */
    public function getOwnBundles()
    {
        $bundles = array();
        $counter = 1;

        $dir = dirname($this->container->getParameter('kernel.root_dir') . '/') . '/src/';
        $files = scandir($dir);
        foreach ($files as $file) {
            if (is_dir($dir . $file) && !in_array($file, array('.', '..'))) {
                $bundleFiles = scandir($dir . $file);
                foreach ($bundleFiles as $bundleFile) {
                    if (is_dir($dir . $file . '/' . $bundleFile) && !in_array($bundleFile, array('.', '..'))) {
                        $bundles[$counter++] = array(
                            'name' => $bundleFile,
                            'namespace' => $file,
                            'dir' => $dir . $file . '/' . $bundleFile
                        );
                    }
                }
            }
        }

        return $bundles;
    }

    /**
     * Returns a list of namespaces as array with a forward slash to split the namespace & bundle.
     *
     * @param Kernel $kernel
     * @return array
     */
    private function getNamespaceAutoComplete(Kernel $kernel)
    {
        $ret = array();
        foreach ($kernel->getBundles() as $k => $v) {
            $ret[] = $this->fixNamespace($v->getNamespace());
        }

        return $ret;
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
     * Asks for the prefix and sets it on the InputInterface as the 'prefix' option, if this option is not set yet.
     * Will set the default to a snake_cased namespace when the namespace has been set on the InputInterface.
     *
     * @param array $text What you want printed before the prefix is asked. If null is provided it'll write a default text.
     * @param string $namespace An optional namespace. If this is set it'll create the default based on this prefix.
     *  If it's not provided it'll check if the InputInterface already has the namespace option.
     *
     * @return string The prefix. But it's also been set on the InputInterface.
     */
    public function askForPrefix(array $text = null, $namespace = null)
    {
        $prefix = $this->input->hasOption('prefix') ? $this->input->getOption('prefix') : null;

        if (is_null($text)) {
            $text = array(
                '',
                'You can add a prefix to the table names of the generated entities for example: <comment>projectname_bundlename_</comment>',
                'Enter an underscore \'_\' if you don\'t want a prefix.',
                ''
            );
        }

        while (is_null($prefix)) {
            if (count($text) > 0) {
                $this->output->writeln($text);
            }

            if (is_null($namespace) || empty($namespace)) {
                $namespace = $this->input->hasOption('namespace') ? $this->input->getOption('namespace') : null;
            } else {
                $namespace = $this->fixNamespace($namespace);
            }
            $defaultPrefix = GeneratorUtils::cleanPrefix($this->convertNamespaceToSnakeCase($namespace));
            $prefix = $this->dialog->ask($this->output, $this->dialog->getQuestion('Tablename prefix', $defaultPrefix), $defaultPrefix);
            $prefix = GeneratorUtils::cleanPrefix($prefix);
            if ($this->input->hasOption('prefix')) {
                $this->input->setOption('prefix', $prefix);
            }

            if($prefix == '') {
                break;
            }

            if(!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $prefix)) {
                $this->output->writeln(sprintf('<bg=red> "%s" contains invalid characters</>', $prefix));
                $prefix = $text = null;
                continue;
            }
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
}
