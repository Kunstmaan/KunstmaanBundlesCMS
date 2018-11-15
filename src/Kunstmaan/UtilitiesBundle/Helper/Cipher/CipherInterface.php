<?php

namespace Kunstmaan\UtilitiesBundle\Helper\Cipher;

/**
 * Cipher interface, classes which extends this interface will make it possible to encrypt and decrypt string values.
 */
interface CipherInterface
{
    /**
     * Encrypt the given value to an unreadable string.
     *
     * @param string $value
     * @param bool   $raw_binary
     *
     * @return string
     */
    public function encrypt($value, $raw_binary = false);

    /**
     * Decrypt the given value so that it's readable again.
     *
     * @param $value
     * @param $raw_binary
     *
     * @return string
     *
     * @internal param string $value
     */
    public function decrypt($value, $raw_binary = false);

    /**
     * @param string $inputFile
     * @param string $outputFile
     *
     * @throws \Defuse\Crypto\Exception\IOException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function encryptFile($inputFile, $outputFile);

    /**
     * @param string $inputFile
     * @param string $outputFile
     *
     * @throws \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     * @throws \Defuse\Crypto\Exception\IOException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function decryptFile($inputFile, $outputFile);
}
