<?php

    namespace Kunstmaan\UtilitiesBundle\Helper\Cipher;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\File;
use Defuse\Crypto\Key;
use Webmozart\Assert\Assert;

/**
 * Cipher, this class can be used to encrypt and decrypt string values.
 */
class Cipher implements CipherInterface
{
    /**
     * @var string $secret
     */
    private $secret;

    /**
     * @param string $secret
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     * @throws \Defuse\Crypto\Exception\BadFormatException
     */
    public function __construct($secret)
    {
        Assert::stringNotEmpty($secret, 'You need to configure a Cipher secret in your parameters.yml before you can use this!');

        $this->secret = $secret;
    }

    /**
     * Encrypt the given value to an unreadable string.
     *
     * @param string $value
     * @param bool $raw_binary
     * @return string
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function encrypt($value, $raw_binary=false)
    {
        return Crypto::encryptWithPassword($value, $this->secret, $raw_binary);
    }

    /**
     * Decrypt the given value so that it's readable again.
     *
     * @param string $value
     * @param bool $raw_binary
     * @return string
     * @throws \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function decrypt($value, $raw_binary=false)
    {
        return Crypto::decryptWithPassword($value, $this->secret, $raw_binary);
    }

    /**
     * @param string $inputFile
     * @param string $outputFile
     * @return void
     * @throws \Defuse\Crypto\Exception\IOException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function encryptFile($inputFile, $outputFile)
    {
        File::encryptFileWithPassword($inputFile, $outputFile, $this->secret);
    }

    /**
     * @param string $inputFile
     * @param string $outputFile
     * @return void
     * @throws \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     * @throws \Defuse\Crypto\Exception\IOException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function decryptFile($inputFile, $outputFile)
    {
        File::decryptFileWithPassword($inputFile, $outputFile, $this->secret);
    }

}
