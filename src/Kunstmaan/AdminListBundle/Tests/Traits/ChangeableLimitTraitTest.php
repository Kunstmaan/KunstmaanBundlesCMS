<?php

namespace Kunstmaan\AdminListBundle\Tests\Traits;

use Kunstmaan\AdminListBundle\AdminList\FilterBuilder;
use Kunstmaan\AdminListBundle\Traits\ChangeableLimitTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class ChangeableLimitTraitTest extends TestCase
{
    /**
     * @dataProvider orderByProvider
     */
    public function testBindRequestOnlyAllowsSortableFieldsAsOrderBy(string $orderBy, string $expected)
    {
        $request = new Request(['orderBy' => $orderBy], [], ['_route' => 'testroute']);
        $request->setSession(new Session(new MockArraySessionStorage()));

        $configurator = $this->createConfigurator();
        $configurator->bindRequest($request);

        $this->assertSame($expected, $configurator->orderBy);
        $this->assertSame($expected, $request->getSession()->get('listconfig_testroute')['orderBy']);
    }

    public function orderByProvider(): iterable
    {
        yield 'sortable field' => ['hello', 'hello'];
        yield 'empty' => ['', ''];
        yield 'unknown field' => ['password', ''];
        yield 'sql injection' => ['hello, (SELECT SLEEP(5))', ''];
        yield 'sql injection with closing bracket' => ['hello) DESC, (SELECT 1]', ''];
    }

    public function testBindRequestSanitizesOrderByFromSession()
    {
        $session = new Session(new MockArraySessionStorage());
        $session->set('listconfig_testroute', ['page' => 1, 'limit' => 10, 'orderBy' => 'hello; DROP TABLE users', 'orderDirection' => 'DESC']);

        $request = new Request([], [], ['_route' => 'testroute']);
        $request->setSession($session);

        $configurator = $this->createConfigurator();
        $configurator->bindRequest($request);

        $this->assertSame('', $configurator->orderBy);
    }

    /**
     * The trait must not depend on AbstractAdminListConfigurator, only on
     * AdminListConfiguratorInterface methods (getSortFields, getFilterBuilder).
     */
    private function createConfigurator(): object
    {
        return new class {
            use ChangeableLimitTrait;

            public $page;
            public $orderBy = '';
            public $orderDirection = '';

            public function getSortFields(): array
            {
                return ['hello', 'world'];
            }

            public function getFilterBuilder(): FilterBuilder
            {
                return new FilterBuilder();
            }
        };
    }
}
