<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\VersionCheck;

use Doctrine\Common\Cache\ArrayCache;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Kunstmaan\AdminBundle\Helper\VersionCheck\Exception\ParseException;
use Kunstmaan\AdminBundle\Helper\VersionCheck\VersionChecker;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class VersionCheckTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject|RequestStack */
    private $requestStack;
    /** @var \PHPUnit\Framework\MockObject\MockObject|LegacyTranslatorInterface|TranslatorInterface */
    private $translator;

    /** @var ContainerInterface (mock) */
    private $container;

    /** @var ArrayAdapter */
    private $cache;

    public function setUp(): void
    {
        $this->cache = $this->createMock(AdapterInterface::class);

        if (\interface_exists(TranslatorInterface::class)) {
            $this->translator = $this->createMock(TranslatorInterface::class);
        } else {
            $this->translator = $this->createMock(LegacyTranslatorInterface::class);
        }

        $requestStack = new RequestStack();
        $requestStack->push(new Request());
        $this->requestStack = $requestStack;
    }

    /**
     * @group legacy
     * @expectedDeprecation Passing an instance of "Symfony\Component\DependencyInjection\ContainerInterface" as the first argument in "Kunstmaan\AdminBundle\Helper\VersionCheck\VersionChecker::__construct" is deprecated since KunstmaanAdminBundle 5.9 and the service parameter types will change in KunstmaanAdminBundle 6.0. Check the constructor arguments and inject the required services and parameters instead.
     * @expectedDeprecation Passing an instance of "Doctrine\Common\Cache\CacheProvider" as the second argument in "Kunstmaan\AdminBundle\Helper\VersionCheck\VersionChecker::__construct" is deprecated since KunstmaanAdminBundle 5.7 and an instance of "Symfony\Component\Cache\Adapter\AdapterInterface" will be required in KunstmaanAdminBundle 6.0.
     */
    public function testDeprecatedCacheConstructorParameter()
    {
        new VersionChecker($this->createMock(ContainerInterface::class), new ArrayCache(), $this->translator);
    }

    /**
     * @group legacy
     * @expectedDeprecation Passing an instance of "Symfony\Component\DependencyInjection\ContainerInterface" as the first argument in "Kunstmaan\AdminBundle\Helper\VersionCheck\VersionChecker::__construct" is deprecated since KunstmaanAdminBundle 5.9 and the service parameter types will change in KunstmaanAdminBundle 6.0. Check the constructor arguments and inject the required services and parameters instead.
     */
    public function testCacheConstructorParameterType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "$cache" parameter should extend from "Doctrine\Common\Cache\CacheProvider" or implement "Symfony\Component\Cache\Adapter\AdapterInterface"');

        new VersionChecker($this->createMock(ContainerInterface::class), new \stdClass(), $this->translator);
    }

    /**
     * @group legacy
     * @expectedDeprecation Passing an instance of "Symfony\Component\DependencyInjection\ContainerInterface" as the first argument in "Kunstmaan\AdminBundle\Helper\VersionCheck\VersionChecker::__construct" is deprecated since KunstmaanAdminBundle 5.9 and the service parameter types will change in KunstmaanAdminBundle 6.0. Check the constructor arguments and inject the required services and parameters instead.
     */
    public function testTranslatorConstructorParameterType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "$translator" parameter should be instance of "Symfony\Contracts\Translation\TranslatorInterface" or "Symfony\Component\Translation\TranslatorInterface"');

        new VersionChecker($this->createMock(ContainerInterface::class), new ArrayCache(), new \stdClass());
    }

    /**
     * @group legacy
     */
    public function testWithoutContainerAndWithoutParamatersConstructorParams()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The first parameter of "Kunstmaan\AdminBundle\Helper\VersionCheck\VersionChecker::__construct" is not of the correct type, inject the correct services and parameters instead.');

        new VersionChecker(new ArrayCache(), $this->translator);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|VersionChecker
     */
    public function setUpVersionCheckerMock(?array $methods, string $projectDir = null)
    {
        $versionCheckerMock = $this->getMockBuilder(VersionChecker::class)
            ->setConstructorArgs([$this->cache, $this->translator, $this->requestStack, 'url', 300, true, $projectDir ?? 'project_dir', 'website_title'])
            ->setMethods($methods)
            ->getMock()
        ;

        return $versionCheckerMock;
    }

    public function testIsEnabled()
    {
        $versionChecker = new VersionChecker(new ArrayAdapter(), $this->translator, $this->requestStack, 'url', 300, true, 'project_dir', 'website_title');

        $this->assertTrue($versionChecker->isEnabled());
    }

    public function testPeriodicallyCheck()
    {
        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem->method('isHit')->willReturn(true);
        $cacheItem->method('get')->willReturn([]);

        $this->cache
            ->expects($this->once())
            ->method('getItem')
            ->willReturn($cacheItem)
        ;
        $versionCheckerMock = $this->setUpVersionCheckerMock(null);
        $versionCheckerMock->periodicallyCheck();
    }

    public function testCheckWithInvalidResponse()
    {
        $versionCheckerMock = $this->setUpVersionCheckerMock(['parseComposer']);
        $versionCheckerMock
            ->expects($this->once())
            ->method('parseComposer')
            ->willReturn(['name' => 'box/spout'])
        ;
        $this->assertFalse($versionCheckerMock->check());
    }

    /**
     * @dataProvider provider
     */
    public function testCheck(string $lockDir, string $expectedType, string $expected)
    {
        if ('exception' === $expectedType) {
            $this->expectException(ParseException::class);
            $this->expectExceptionMessage($expected);
        }

        $this->translator = $this->createMock(Translator::class);
        $this->translator
            ->expects($this->any())
            ->method('trans')
            ->willReturn('translated')
        ;

        if ('instanceOf' === $expectedType) {
            $cacheItem = $this->createMock(CacheItemInterface::class);
            $cacheItem->method('isHit')->willReturn(false);
            $cacheItem->expects($this->once())->method('expiresAfter')->with(300);
            $cacheItem->expects($this->once())->method('set')->with($this->isInstanceOf($expected));

            $this->cache
                ->expects($this->once())
                ->method('getItem')
                ->willReturn($cacheItem);
        }

        $mock = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'], \json_encode(['foo' => 'bar'])),
        ]);
        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $versionChecker = new VersionChecker($this->cache, $this->translator, $this->requestStack, 'https://www.example.com', 300, true, $lockDir, 'website_title');
        $versionChecker->setClient($client);

        if ('instanceOf' === $expectedType) {
            $this->assertInstanceOf($expected, $versionChecker->check());
        } else {
            $versionChecker->check();
        }
    }

    public function provider()
    {
        $baseDir = __DIR__ . '/testdata';

        return [
            'composer.lock ok' => [$baseDir . '/composer_ok', 'instanceOf', \stdClass::class],
            'composer.lock broken' => [$baseDir . '/composer_broken', 'exception', 'translated (#4)'],
            'composer.lock bundleless' => [$baseDir . '/composer_bundleless', 'exception', 'translated'],
            'composer.lock not found' => [$baseDir . '/composer_not_there', 'exception', 'translated'],
        ];
    }
}
