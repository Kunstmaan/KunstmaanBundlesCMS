<?php

namespace Kunstmaan\UtilitiesBundle\Tests\Helper\Cipher;

use Kunstmaan\UtilitiesBundle\Helper\Cipher\Cipher;
use PHPUnit\Framework\TestCase;

class CipherTest extends TestCase
{
    const SECRET = 'secret';
    const CONTENT = 'This is a random sentence which will be encrypted and then decrypted!';

    /**
     * @var Cipher
     */
    protected $cipher;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @covers \Kunstmaan\UtilitiesBundle\Helper\Cipher\Cipher::__construct
     */
    protected function setUp(): void
    {
        $this->cipher = new Cipher(self::SECRET);
    }

    /**
     * @covers \Kunstmaan\UtilitiesBundle\Helper\Cipher\Cipher::encrypt
     * @covers \Kunstmaan\UtilitiesBundle\Helper\Cipher\Cipher::decrypt
     */
    public function testEncryptAndDecrypt()
    {
        $encryptedValue = $this->cipher->encrypt(self::CONTENT);
        $this->assertNotEquals(self::CONTENT, $encryptedValue);
        $decryptedValue = $this->cipher->decrypt($encryptedValue);
        $this->assertEquals(self::CONTENT, $decryptedValue);
    }

    public function testException()
    {
        $this->expectException(\Exception::class);
        new Cipher('');
    }
}
