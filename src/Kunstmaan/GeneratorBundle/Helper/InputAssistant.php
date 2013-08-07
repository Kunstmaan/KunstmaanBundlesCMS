<?php

namespace Kunstmaan\GeneratorBundle\Helper;


use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper,
    Sensio\Bundle\GeneratorBundle\Command\Validators;

use Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\HttpKernel\Kernel;

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

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param DialogHelper    $dialog
     */
    public function __construct(InputInterface &$input, OutputInterface $output, DialogHelper $dialog, Kernel $kernel)
    {
        $this->input = $input;
        $this->output = $output;
        $this->dialog = $dialog;
        $this->kernel = $kernel;
    }

    /**
     * Asks for the namespace and sets it on the InputInterface as the 'namespace' option, if this option is not set yet.
     *
     * @param array $text    What you want printed before the namespace is asked.
     *
     * @return string The namespace. But it's also been set on the InputInterface.
     */
    public function askForNamespace(array $text = null)
    {
        $namespace = null;

        try {
            $namespace = $this->input->getOption('namespace') ? Validators::validateBundleNamespace($this->input->getOption('namespace')) : null;
        } catch (\Exception $error) {
            $this->output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }

        $namespaces = $this->getNamespaceAutoComplete($this->kernel);

        while (true) {
            if (!is_null($text) && (count($text) > 0)) {
                $this->output->writeln($text);
            }

            $namespace = $this->dialog->askAndValidate($this->output, $this->dialog->getQuestion('Bundle Namespace', $namespace), array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateBundleNamespace'), false, null, $namespaces);

            try {
                Validators::validateBundleNamespace($namespace);
                break;
            }  catch (\Exception $e) {
                $this->output->writeln(sprintf('<bg=red>Namespace "%s" does not exist.</>', $namespace));
            }
        }

        $this->input->setOption('namespace', $namespace);

        return $namespace;
    }

    /**
     * Asks for the prefix and sets it on the InputInterface as the 'prefix' option, if this option is not set yet.
     * Will set the default to a snake_cased namespace when the namespace has been set on the InputInterface.
     *
     * @param array  $text What you want printed before the prefix is asked.
     * @param string $namespace An optional namespace. If this is set it'll create the default based on this prefix.
     *  If it's not provided it'll check if the InputInterface already has the namespace option.
     *
     * @return string The prefix. But it's also been set on the InputInterface.
     */
    public function askForPrefix(array $text = null, $namespace = null)
    {
        $prefix = $this->input->getOption('prefix') ? $this->input->getOption('prefix') : null;

        if (is_null($prefix)) {
            if (!is_null($text) && (count($text) > 0)) {
                $this->output->writeln($text);
            }

            if (is_null($namespace) || empty($namespace)) {
                $namespace = $this->input->getOption('namespace');
            } else {
                $namespace = $this->fixNamespace($namespace);
            }
            $defaultPrefix = $this->convertNamespaceToSnakeCase($namespace);
            $prefix = $this->dialog->ask($this->output, $this->dialog->getQuestion('Tablename prefix', $defaultPrefix), $defaultPrefix);

            $this->input->setOption('prefix', GeneratorUtils::cleanPrefix($prefix));
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

        $namespace = $this->fixNamespace($namespace);

        $parts = explode('/', $namespace);
        $parts = array_map(function($k) {
            return strtolower($k);
        }, $parts);

        return implode('_', $parts);
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
}