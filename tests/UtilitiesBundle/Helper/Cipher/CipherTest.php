<?php

namespace Tests\Kunstmaan\UtilitiesBundle\Helper\Cipher;

use Kunstmaan\UtilitiesBundle\Helper\Cipher\Cipher;

/**
 * CipherTest
 */
class CipherTest extends \PHPUnit_Framework_TestCase
{

    const SECRET = "secret";
    const CONTENT = "This is a random sentence which will be encrypted and then decrypted!";

    /**
     * @var Cipher
     */
    protected $cipher;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->cipher = new Cipher(self::SECRET);
    }

    public function testEncryptDecrypt()
    {
        $encryptedValue = $this->cipher->encrypt(self::CONTENT);
        $this->assertNotEquals(self::CONTENT, $encryptedValue);
        $decryptedValue = $this->cipher->decrypt($encryptedValue);
        $this->assertEquals($decryptedValue, self::CONTENT);
    }

}
