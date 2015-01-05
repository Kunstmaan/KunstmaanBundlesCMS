<?php

namespace Kunstmaan\GeneratorBundle\Helper;

use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Kernel;

class CommandAssistant
{
    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var DialogHelper
     */
    private $dialog;

    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @param $input InputInterface
     */
    public function setInput(InputInterface $input)
    {
        $this->input = $input;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param $output OutputInterface
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param $dialog DialogHelper
     */
    public function setDialog(DialogHelper $dialog)
    {
        $this->dialog = $dialog;
    }

    /**
     * @return Kernel
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * @param $kernel Kernel
     */
    public function setKernel(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function writeSection($text, $style = 'bg=blue;fg=white')
    {
        $this->getDialog()->writeSection($this->output, $text, $style);
    }

    /**
     * @return DialogHelper
     */
    private function getDialog()
    {
        return $this->dialog;
    }

    public function writeLine($text, $type = OutputInterface::OUTPUT_NORMAL)
    {
        $this->output->writeln($text, $type);
    }

    public function write($text, $newLine = false, $type = OutputInterface::OUTPUT_NORMAL)
    {
        $this->output->write($text, $newLine, $type);
    }

    public function writeError($message, $exit = false)
    {
        $this->output->writeln($this->getDialog()->getHelperSet()->get('formatter')->formatBlock($message, 'error'));
        if ($exit) {
            exit;
        }
    }

    public function askAndValidate($question, $validator, $defaultValue = null, array $autoComplete = null)
    {
        return $this->getDialog()->askAndValidate($this->output, $this->getDialog()->getQuestion($question, $defaultValue), $validator, false, $defaultValue, $autoComplete);
    }

    public function askConfirmation($question, $defaultString, $separator = '?', $defaultValue = true)
    {
        return $this->getDialog()->askConfirmation($this->output, $this->getDialog()->getQuestion($question, $defaultString, $separator), $defaultValue);
    }

    public function ask($question, $default = null, array $autoComplete = null)
    {
        return $this->getDialog()->ask($this->output, $this->getDialog()->getQuestion($question, $default), $default, $autoComplete);
    }

    public function askSelect($question, $choices, $default = null, $multiSelect = false, $errorMessage = 'Value "%s" is invalid')
    {
        $bundleQuestion = $this->dialog->getQuestion($question, $default);
        return $this->dialog->select($this->output, $bundleQuestion, $choices, $default, false, $errorMessage, $multiSelect);
    }

    public function setOption($name, $value)
    {
        $this->input->setOption($name, $value);
    }

    public function hasOption($name)
    {
        return $this->input->hasOption($name);
    }

    public function getOption($name)
    {
        return $this->input->getOption($name);
    }

    public function isInteractive()
    {
        return $this->input->isInteractive();
    }

    public function getOptionOrDefault($option, $default = null)
    {
        return $this->input->hasOption($option) ? $this->input->getOption($option) : $default;
    }
}
