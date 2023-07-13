<?php

namespace Kunstmaan\NodeBundle\Tests\Helper\Validation;

use Kunstmaan\NodeBundle\Validation\URLValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UrlValidatorTest extends TestCase
{
    /**
     * @var URLValidator|MockObject
     */
    private MockObject $validatorMock;

    protected function setUp(): void
    {
        $this->validatorMock = $this->getMockForTrait(URLValidator::class);
    }

    /**
     * @dataProvider emailAddressProvider
     */
    public function testIsEmailAddress($email, $result)
    {
        $this->assertSame($result, $this->validatorMock->isEmailAddress($email));
    }

    /**
     * @dataProvider internalLinkProvider
     */
    public function testIsInternalLink($link, $result)
    {
        $this->assertSame($result, $this->validatorMock->isInternalLink($link));
    }

    /**
     * @dataProvider internalMediaLinkProvider
     */
    public function testIsInternalMediaLink($link, $result)
    {
        $this->assertSame($result, $this->validatorMock->isInternalMediaLink($link));
    }

    public function emailAddressProvider(): \Iterator
    {
        yield ['abc', false];
        yield ['test@example.com', 'test@example.com'];
        yield ['test@local', false];
    }

    public function internalLinkProvider(): \Iterator
    {
        yield ['[NT:123]', false];
        yield ['[NT123]', true];
        yield ['[NTABC]', false];
        yield ['[NT123ABC]', false];
        yield ['[host_a:NT123]', true];
        yield ['http://www.google.com', false];
        yield ['[NT123][M20]', true];
    }

    public function internalMediaLinkProvider(): \Iterator
    {
        yield ['[M:123]', false];
        yield ['[M123]', true];
        yield ['[MABC]', false];
        yield ['[M123ABC]', false];
        yield ['[host_a:M23]', true];
        yield ['http://www.google.com', false];
        yield ['[M123][NT20]', true];
    }
}
