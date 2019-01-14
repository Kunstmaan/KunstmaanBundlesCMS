<?php

namespace Kunstmaan\UtilitiesBundle\Command;

use Kunstmaan\UtilitiesBundle\Helper\Cipher\CipherInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @final since 5.1
 * NEXT_MAJOR extend from `Command` and remove `$this->getContainer` usages
 */
class CipherCommand extends ContainerAwareCommand
{
    /**
     * @var CipherInterface
     */
    private $cipher;

    private static $methods = [
        0 => 'Encrypt text',
        1 => 'Decrypt text',
        2 => 'Encrypt file',
        3 => 'Decrypt file',
    ];

    /**
     * @param CipherInterface|null $cipher
     */
    public function __construct(/* CipherInterface */ $cipher = null)
    {
        parent::__construct();

        if (!$cipher instanceof CipherInterface) {
            @trigger_error(sprintf('Passing a command name as the first argument of "%s" is deprecated since version symfony 3.4 and will be removed in symfony 4.0. If the command was registered by convention, make it a service instead. ', __METHOD__), E_USER_DEPRECATED);

            $this->setName(null === $cipher ? 'kuma:cipher' : $cipher);

            return;
        }

        $this->cipher = $cipher;
    }

    protected function configure()
    {
        $this->setName('kuma:cipher')->setDescription('Cipher utilities commands.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null === $this->cipher) {
            $this->cipher = $this->getContainer()->get('kunstmaan_utilities.cipher');
        }
        $helper = $this->getHelper('question');

        $question = new ChoiceQuestion(
            'Please select the method you want to use',
            self::$methods,
            0
        );

        $question->setErrorMessage('Method %s is invalid.');
        $method = $helper->ask($input, $output, $question);
        $method = array_search($method, self::$methods, true);
        switch ($method) {
            case 0:
            case 1:
                $question = new Question('Please enter the text: ');
                $question->setValidator(function ($value) {
                    if (trim($value) === '') {
                        throw new \Exception('The text cannot be empty');
                    }

                    return $value;
                });
                $question->setMaxAttempts(3);
                $text = $helper->ask($input, $output, $question);
                $text = $method === 0 ? $this->cipher->encrypt($text) : $this->cipher->decrypt($text);
                $output->writeln(sprintf('Result: %s', $text));

                break;
            case 2:
            case 3:
            $fs = new Filesystem();

            $question = new Question('Please enter the input file path: ');
                $question->setValidator(function ($value) use ($fs) {
                    if (trim($value) === '') {
                        throw new \Exception('The input file path cannot be empty');
                    }

                    if (false === $fs->exists($value)) {
                        throw new \Exception('The input file must exists');
                    }

                    if (is_dir($value)) {
                        throw new \Exception('The input file cannot be a dir');
                    }

                    return $value;
                });
                $question->setMaxAttempts(3);
                $inputFilePath = $helper->ask($input, $output, $question);

                $question = new Question('Please enter the output file path: ');
                $question->setValidator(function ($value) {
                    if (trim($value) === '') {
                        throw new \Exception('The output file path cannot be empty');
                    }

                    if (is_dir($value)) {
                        throw new \Exception('The output file path cannot be a dir');
                    }

                    return $value;
                });
                $question->setMaxAttempts(3);
                $outputFilePath = $helper->ask($input, $output, $question);

                if ($method === 2) {
                    $this->cipher->encryptFile($inputFilePath, $outputFilePath);
                } else {
                    if (false === $fs->exists($outputFilePath)) {
                        $fs->touch($outputFilePath);
                    }
                    $this->cipher->decryptFile($inputFilePath, $outputFilePath);
                }

                $output->writeln(sprintf('Check "%s" to see result', $outputFilePath));

                break;
        }
    }
}
