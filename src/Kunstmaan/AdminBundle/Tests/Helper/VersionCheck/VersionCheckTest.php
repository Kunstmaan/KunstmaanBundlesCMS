<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\VersionCheck;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Kunstmaan\AdminBundle\Helper\VersionCheck\Exception\ParseException;
use Kunstmaan\AdminBundle\Helper\VersionCheck\VersionChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

class VersionCheckTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject|RequestStack */
    private $requestStack;
    /** @var \PHPUnit\Framework\MockObject\MockObject|TranslatorInterface */
    private $translator;
    /** @var ArrayAdapter */
    private $cache;

    public function setUp(): void
    {
        $this->cache = new ArrayAdapter();
        $this->translator = $this->createMock(TranslatorInterface::class);

        $requestStack = new RequestStack();
        $requestStack->push(new Request());
        $this->requestStack = $requestStack;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|VersionChecker
     */
    public function setUpVersionCheckerMock(?array $methods, ?string $projectDir = null)
    {
        $versionCheckerMock = $this->getMockBuilder(VersionChecker::class)
            ->setConstructorArgs([$this->cache, $this->translator, $this->requestStack, 'url', 300, true, $projectDir ?? 'project_dir', 'website_title'])
            ->onlyMethods($methods ?? [])
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
        $cacheItem = $this->cache->getItem(VersionChecker::CACHE_KEY);
        $cacheItem->set([]);

        $this->cache->save($cacheItem);

        $versionCheckerMock = $this->setUpVersionCheckerMock(['check']);
        $versionCheckerMock->expects($this->never())->method('check');
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
