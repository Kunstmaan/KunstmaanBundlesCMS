<?php

namespace Kunstmaan\UtilitiesBundle\Tests\Helper\Cipher;

use Kunstmaan\UtilitiesBundle\Helper\Cipher\Cipher;

class CipherTest extends \PHPUnit_Framework_TestCase
{

    const SECRET = "secret";

    /*
     * @var Cipher
     */
    protected $cipher;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @covers Kunstmaan\UtilitiesBundle\Helper\Cipher\Cipher::__construct
     */
    protected function setUp()
    {
        $this->cipher = new Cipher(self::SECRET);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\UtilitiesBundle\Helper\Cipher\Cipher::encrypt
     * @covers Kunstmaan\UtilitiesBundle\Helper\Cipher\Cipher::decrypt
     */
    public function testEncryptDecrypt()
    {
        $content = "This is a random sentence which will be encrypted and then decrypted!";
        $encryptedValue = $this->cipher->encrypt($content);
        $this->assertNotEquals($content, $encryptedValue);
        $decryptedValue = $this->cipher->decrypt($encryptedValue);
    }

}
