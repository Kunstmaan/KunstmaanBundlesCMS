<?php

namespace Kunstmaan\GeneratorBundle\Helper;

use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
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
     * @var QuestionHelper
     */
    private $questionHelper;

    /**
     * @var Kernel
     */
    private $kernel;

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

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function setQuestionHelper(QuestionHelper $questionHelper)
    {
        $this->questionHelper = $questionHelper;
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
        $this->getQuestionHelper()->writeSection($this->output, $text, $style);
    }

    /**
     * @return Questionhelper
     */
    private function getQuestionHelper()
    {
        return $this->questionHelper;
    }

    public function writeLine($text, $type = OutputInterface::OUTPUT_NORMAL)
    {
        $this->output->writeln($text, $type);
    }

    public function write(
        $text,
        $newLine = false,
        $type = OutputInterface::OUTPUT_NORMAL
    ) {
        $this->output->write($text, $newLine, $type);
    }

    public function writeError($message, $exit = false)
    {
        $this->output->writeln(
            $this->getQuestionHelper()
                ->getHelperSet()
                ->get('formatter')
                ->formatBlock($message, 'error')
        );
        if ($exit) {
            exit;
        }
    }

    public function askAndValidate(
        $question,
        $validator,
        $defaultValue = null,
        array $autoComplete = null
    ) {
        $validationQuestion = new Question(
            $this->getQuestionHelper()->getQuestion($question, $defaultValue),
            $defaultValue
        );
        $validationQuestion->setAutocompleterValues($autoComplete);
        $validationQuestion->setValidator($validator);

        return $this->getQuestionHelper()->ask(
            $this->input,
            $this->output,
            $validationQuestion
        );
    }

    public function askConfirmation(
        $question,
        $defaultString,
        $separator = '?',
        $defaultValue = true
    ) {
        $confirmationQuestion = new ConfirmationQuestion(
            $this->getQuestionHelper()->getQuestion(
                $question,
                $defaultString,
                $separator
            ), $defaultValue
        );

        return $this->getQuestionHelper()->ask(
            $this->input,
            $this->output,
            $confirmationQuestion
        );
    }

    public function ask($question, $default = null, array $autoComplete = null)
    {
        $askQuestion = new Question(
            $this->questionHelper->getQuestion($question, $default), $default
        );
        $askQuestion->setAutocompleterValues($autoComplete);

        return $this->getQuestionHelper()->ask(
            $this->input,
            $this->output,
            $askQuestion
        );
    }

    public function askSelect(
        $question,
        $choices,
        $default = null,
        $multiSelect = false,
        $errorMessage = 'Value "%s" is invalid'
    ) {
        $bundleQuestion = new ChoiceQuestion(
            $this->getQuestionHelper()->getQuestion($question, $default),
            $choices
        );
        $bundleQuestion->setErrorMessage($errorMessage);
        $bundleQuestion->setMultiselect($multiSelect);
        if ($multiSelect) {
            $toReturn = [];
            foreach ($this->getQuestionHelper()->ask(
                $this->input,
                $this->output,
                $bundleQuestion
            ) as $each) {
                array_push(
                    $toReturn,
                    array_search($each, $bundleQuestion->getChoices())
                );
            }

            return $toReturn;
        } else {
            $value = $this->getQuestionHelper()->ask(
                $this->input,
                $this->output,
                $bundleQuestion
            );

            return array_search($value, $bundleQuestion->getChoices());
        }
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
        return $this->input->hasOption($option) ? $this->input->getOption(
            $option
        ) : $default;
    }
}
