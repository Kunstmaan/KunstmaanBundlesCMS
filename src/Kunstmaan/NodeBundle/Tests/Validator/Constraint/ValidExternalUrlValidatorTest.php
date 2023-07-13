<?php

namespace Kunstmaan\NodeBundle\Tests\Validator\Constraint;

use Kunstmaan\NodeBundle\Validator\Constraint\ValidExternalUrl;
use Kunstmaan\NodeBundle\Validator\Constraint\ValidExternalUrlValidator;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class ValidExternalUrlValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        return new ValidExternalUrlValidator();
    }

    /**
     * @dataProvider getValidUrls
     */
    public function testValidUrls($url)
    {
        $this->validator->validate($url, new ValidExternalUrl());

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidUrls
     */
    public function testInvalidUrls($url)
    {
        $this->validator->validate($url, new ValidExternalUrl());

        $this->buildViolation('This value is not a valid URL.')
            ->setParameter('{{ value }}', '"' . $url . '"')
            ->setCode(Url::INVALID_URL_ERROR)
            ->assertRaised();
    }

    public function getValidUrls(): \Iterator
    {
        yield ['http://www.example.com'];
        yield ['https://example.com'];
        yield ['#'];
        yield ['#anchor-name'];
        yield ['#!'];
    }

    public function getInvalidUrls(): \Iterator
    {
        yield ['example.com'];
        yield ['www.example.com'];
        yield ['!#'];
        yield ['abc#anchor-name'];
    }
}
