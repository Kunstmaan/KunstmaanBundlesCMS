<?php

namespace Tests\Kunstmaan\UtilitiesBundle\Helper\Cipher;

use Kunstmaan\UtilitiesBundle\Helper\Cipher\UrlSafeCipher;

/**
 * UrlSafeCipherTest
 */
class UrlSafeCipherTest extends \PHPUnit_Framework_TestCase
{
    const SECRET = 'secret';
    const CONTENT = 'This is a random sentence which will be encrypted and then decrypted!';

    /**
     * @var UrlSafeCipher
     */
    protected $cipher;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @covers \Kunstmaan\UtilitiesBundle\Helper\Cipher\UrlSafeCipher::__construct
     */
    protected function setUp()
    {
        $this->cipher = new UrlSafeCipher(self::SECRET);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers \Kunstmaan\UtilitiesBundle\Helper\Cipher\UrlSafeCipher::encrypt
     * @covers \Kunstmaan\UtilitiesBundle\Helper\Cipher\UrlSafeCipher::decrypt
     */
    public function testEncryptDecrypt()
    {
        $encryptedValue = $this->cipher->encrypt(self::CONTENT);
        $this->assertNotEquals(self::CONTENT, $encryptedValue);
        $decryptedValue = $this->cipher->decrypt($encryptedValue);
        $this->assertEquals($decryptedValue, self::CONTENT);
    }

    /**
     * @covers \Kunstmaan\UtilitiesBundle\Helper\Cipher\UrlSafeCipher::hex2bin
     */
    public function testHex2bin()
    {
        $hexValue = bin2hex(self::CONTENT);
        $this->assertNotEquals(self::CONTENT, $hexValue);
        $binValue = $this->cipher->hex2bin($hexValue);
        $this->assertEquals($binValue, self::CONTENT);
    }
}
