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

        $this->secret = Key::loadFromAsciiSafeString(
            $this->generateSecret($secret)
        );
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
        return Crypto::encrypt($value, $this->secret, $raw_binary);
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
        return Crypto::decrypt($value, $this->secret, $raw_binary);
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
        File::encryptFile($inputFile, $outputFile, $this->secret);
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
        File::decryptFile($inputFile, $outputFile, $this->secret);
    }

    /**
     * @param string $secret
     * @return string
     */
    private function generateSecret($secret)
    {
        // Need to be implement if we wont use secret like that
        // def00000290a4b250a1b24c41f3076b5e3955e1a51d8535a5dbcf209d17f1eb8d772349cbd12af5dc8f4b05d43ca900489c0fb5aa5c4c5190ccffb5663ae4831e3022fc6

        return $secret;
    }

}

