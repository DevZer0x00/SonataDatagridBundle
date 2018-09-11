<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DatagridBundle\Tests\Pager;

use PHPUnit\Framework\TestCase;
use Sonata\DatagridBundle\Pager\BasePager;

/**
 * @author Andrej Hudec <pulzarraider@gmail.com>
 */
class BasePagerTest extends TestCase
{
    /**
     * @var BasePager
     */
    private $pager;

    protected function setUp(): void
    {
        $this->pager = $this->getMockForAbstractClass('Sonata\DatagridBundle\Pager\BasePager');
    }

    /**
     * @dataProvider getGetMaxPerPage1Tests
     */
    public function testGetMaxPerPage1($expectedMaxPerPage, $expectedPage, $maxPerPage, $page): void
    {
        $this->assertEquals(10, $this->pager->getMaxPerPage());
        $this->assertEquals(1, $this->pager->getPage());

        if (null !== $page) {
            $this->pager->setPage($page);
        }

        $this->pager->setMaxPerPage($maxPerPage);

        $this->assertEquals($expectedPage, $this->pager->getPage());
        $this->assertEquals($expectedMaxPerPage, $this->pager->getMaxPerPage());
    }

    public function getGetMaxPerPage1Tests()
    {
        return [
            [123, 1, 123, 1],
            [123, 321, 123, 321],
            [1, 1, 1, 0],
            [0, 0, 0, 0],
            [1, 1, -1, 1],
            [1, 1, -1, 0],
            [1, 1, -1, -1],
            [0, 0, 0, null],
        ];
    }

    public function testGetMaxPerPage2(): void
    {
        $this->assertEquals(10, $this->pager->getMaxPerPage());
        $this->assertEquals(1, $this->pager->getPage());

        $this->pager->setMaxPerPage(0);
        $this->pager->setPage(0);

        $this->assertEquals(0, $this->pager->getMaxPerPage());
        $this->assertEquals(0, $this->pager->getPage());

        $this->pager->setMaxPerPage(12);

        $this->assertEquals(12, $this->pager->getMaxPerPage());
        $this->assertEquals(1, $this->pager->getPage());
    }

    public function testGetMaxPerPage3(): void
    {
        $this->assertEquals(10, $this->pager->getMaxPerPage());
        $this->assertEquals(1, $this->pager->getPage());

        $this->pager->setMaxPerPage(0);

        $this->assertEquals(0, $this->pager->getMaxPerPage());
        $this->assertEquals(0, $this->pager->getPage());

        $this->pager->setMaxPerPage(-1);

        $this->assertEquals(1, $this->pager->getMaxPerPage());
        $this->assertEquals(1, $this->pager->getPage());
    }

    public function testGetCurrentMaxLink(): void
    {
        $this->assertEquals(1, $this->pager->getCurrentMaxLink());

        $this->pager->getLinks();
        $this->assertEquals(1, $this->pager->getCurrentMaxLink());

        $this->callMethod($this->pager, 'setLastPage', [20]);
        $this->pager->getLinks(10);
        $this->assertEquals(10, $this->pager->getCurrentMaxLink());

        $this->pager->setMaxPageLinks(5);
        $this->pager->setPage(2);
        $this->assertEquals(10, $this->pager->getCurrentMaxLink());
    }

    public function testGetMaxRecordLimit(): void
    {
        $this->assertEquals(0, $this->pager->getMaxRecordLimit());

        $this->pager->setMaxRecordLimit(99);
        $this->assertEquals(99, $this->pager->getMaxRecordLimit());
    }

    public function testGetNbResults(): void
    {
        $this->assertEquals(0, $this->pager->getNbResults());

        $this->callMethod($this->pager, 'setNbResults', [100]);

        $this->assertEquals(100, $this->pager->getNbResults());
    }

    public function testCount(): void
    {
        $this->assertEquals(0, $this->pager->count());

        $this->callMethod($this->pager, 'setNbResults', [100]);

        $this->assertEquals(100, $this->pager->count());
    }

    public function testGetQuery(): void
    {
        $query = $this->createMock('Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface');

        $this->pager->setQuery($query);
        $this->assertEquals($query, $this->pager->getQuery());
    }

    public function testGetCountColumn(): void
    {
        $this->assertEquals(['id'], $this->pager->getCountColumn());

        $this->pager->setCountColumn(['foo']);
        $this->assertEquals(['foo'], $this->pager->getCountColumn());
    }

    public function testParameters(): void
    {
        $this->assertNull($this->pager->getParameter('foo', null));
        $this->assertEquals('bar', $this->pager->getParameter('foo', 'bar'));
        $this->assertFalse($this->pager->hasParameter('foo'));
        $this->assertEquals([], $this->pager->getParameters());

        $this->pager->setParameter('foo', 'foo_value');

        $this->assertTrue($this->pager->hasParameter('foo'));
        $this->assertEquals('foo_value', $this->pager->getParameter('foo', null));
        $this->assertEquals('foo_value', $this->pager->getParameter('foo', 'bar'));
        $this->assertEquals(['foo' => 'foo_value'], $this->pager->getParameters());

        $this->pager->setParameter('foo', 'baz');

        $this->assertTrue($this->pager->hasParameter('foo'));
        $this->assertEquals('baz', $this->pager->getParameter('foo', null));
        $this->assertEquals('baz', $this->pager->getParameter('foo', 'bar'));
        $this->assertEquals(['foo' => 'baz'], $this->pager->getParameters());

        $this->pager->setParameter('foo2', 'foo2_value');

        $this->assertTrue($this->pager->hasParameter('foo2'));
        $this->assertEquals('foo2_value', $this->pager->getParameter('foo2', null));
        $this->assertEquals('foo2_value', $this->pager->getParameter('foo2', 'bar'));
        $this->assertEquals(['foo' => 'baz', 'foo2' => 'foo2_value'], $this->pager->getParameters());
    }

    public function testGetMaxPageLinks(): void
    {
        $this->assertEquals(0, $this->pager->getMaxPageLinks());

        $this->pager->setMaxPageLinks(123);
        $this->assertEquals(123, $this->pager->getMaxPageLinks());
    }

    public function testIsFirstPage(): void
    {
        $this->assertTrue($this->pager->isFirstPage());

        $this->pager->setPage(123);
        $this->assertFalse($this->pager->isFirstPage());
    }

    public function testIsLastPage(): void
    {
        $this->assertTrue($this->pager->isLastPage());
        $this->assertEquals(1, $this->pager->getLastPage());

        $this->pager->setPage(10);
        $this->callMethod($this->pager, 'setLastPage', [50]);
        $this->assertEquals(50, $this->pager->getLastPage());
        $this->assertFalse($this->pager->isLastPage());

        $this->pager->setPage(50);
        $this->assertTrue($this->pager->isLastPage());

        $this->callMethod($this->pager, 'setLastPage', [20]);
        $this->assertEquals(20, $this->pager->getPage());
        $this->assertEquals(20, $this->pager->getLastPage());
        $this->assertTrue($this->pager->isLastPage());
    }

    public function testGetLinks(): void
    {
        $this->assertEquals([], $this->pager->getLinks());

        $this->pager->setPage(1);
        $this->pager->setMaxPageLinks(1);
        $this->assertEquals([1], $this->pager->getLinks());
        $this->assertEquals([1], $this->pager->getLinks(10));

        $this->pager->setPage(1);
        $this->pager->setMaxPageLinks(7);
        $this->callMethod($this->pager, 'setLastPage', [50]);
        $this->assertCount(7, $this->pager->getLinks());
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7], $this->pager->getLinks());

        $this->pager->setPage(10);
        $this->pager->setMaxPageLinks(12);
        $this->assertCount(5, $this->pager->getLinks(5));
        $this->assertEquals([8, 9, 10, 11, 12], $this->pager->getLinks(5));

        $this->pager->setPage(10);
        $this->pager->setMaxPageLinks(6);
        $this->assertCount(6, $this->pager->getLinks());
        $this->assertEquals([7, 8, 9, 10, 11, 12], $this->pager->getLinks());

        $this->pager->setPage(50);
        $this->pager->setMaxPageLinks(6);
        $this->assertCount(6, $this->pager->getLinks());
        $this->assertEquals([45, 46, 47, 48, 49, 50], $this->pager->getLinks());
    }

    public function testHaveToPaginate(): void
    {
        $this->assertFalse($this->pager->haveToPaginate());

        $this->pager->setMaxPerPage(10);
        $this->assertFalse($this->pager->haveToPaginate());

        $this->callMethod($this->pager, 'setNbResults', [100]);
        $this->assertTrue($this->pager->haveToPaginate());
    }

    public function testIterator(): void
    {
        $this->assertTrue($this->pager instanceof \Iterator);

        $object1 = new \stdClass();
        $object1->foo = 'bar1';

        $object2 = new \stdClass();
        $object2->foo = 'bar2';

        $object3 = new \stdClass();
        $object3->foo = 'bar3';

        $expectedObjects = [$object1, $object2, $object3];

        $this->pager->expects($this->any())
            ->method('getResults')
            ->will($this->returnValue($expectedObjects));

        $counter = 0;
        $values = [];
        foreach ($this->pager as $key => $value) {
            $values[$key] = $value;
            ++$counter;
        }

        $this->assertEquals(3, $counter);
        $this->assertEquals($object3, $value);
        $this->assertEquals($expectedObjects, $values);

        $this->assertFalse($this->pager->valid());

        $this->callMethod($this->pager, 'resetIterator');
        $this->assertTrue($this->pager->valid());
    }

    public function testValid(): void
    {
        $this->pager->expects($this->any())
            ->method('getResults')
            ->will($this->returnValue([]));

        $this->assertFalse($this->pager->valid());
    }

    public function testNext(): void
    {
        $this->pager->expects($this->any())
            ->method('getResults')
            ->will($this->returnValue([]));

        $this->assertFalse($this->pager->next());
    }

    public function testKey(): void
    {
        $this->pager->expects($this->any())
            ->method('getResults')
            ->will($this->returnValue([123 => new \stdClass()]));

        $this->assertEquals(123, $this->pager->key());
    }

    public function testCurrent(): void
    {
        $object = new \stdClass();

        $this->pager->expects($this->any())
            ->method('getResults')
            ->will($this->returnValue([$object]));

        $this->assertEquals($object, $this->pager->current());
    }

    public function testGetCursor(): void
    {
        $this->assertEquals(1, $this->pager->getCursor());

        $this->pager->setCursor(0);
        $this->assertEquals(1, $this->pager->getCursor());

        $this->pager->setCursor(300);
        $this->assertEquals(0, $this->pager->getCursor());

        $this->callMethod($this->pager, 'setNbResults', [100]);

        $this->pager->setCursor(5);
        $this->assertEquals(5, $this->pager->getCursor());

        $this->pager->setCursor(300);
        $this->assertEquals(100, $this->pager->getCursor());
    }

    public function testGetObjectByCursor(): void
    {
        $object1 = new \stdClass();
        $object1->foo = 'bar1';

        $object2 = new \stdClass();
        $object2->foo = 'bar2';

        $object3 = new \stdClass();
        $object3->foo = 'bar3';

        $this->callMethod($this->pager, 'setNbResults', [3]);

        $query = $this->createMock('Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface');

        $query->expects($this->any())
            ->method('setFirstResult')
            ->will($this->returnValue($query));

        $query->expects($this->any())
            ->method('setMaxResults')
            ->will($this->returnValue($query));

        $id = 0;
        $query->expects($this->any())
            ->method('execute')
            ->will($this->returnCallback(function () use (&$id, $object1, $object2, $object3) {
                switch ($id) {
                    case 0:
                        return [$object1];

                        break;

                    case 1:
                        return [$object2];

                        break;

                    case 2:
                        return [$object3];

                        break;
                }
            }));

        $this->pager->setQuery($query);

        $this->assertEquals($object1, $this->pager->getObjectByCursor(1));
        $this->assertEquals(1, $this->pager->getCursor());

        $id = 1;
        $this->assertEquals($object2, $this->pager->getObjectByCursor(2));
        $this->assertEquals(2, $this->pager->getCursor());

        $id = 2;
        $this->assertEquals($object3, $this->pager->getObjectByCursor(3));
        $this->assertEquals(3, $this->pager->getCursor());

        $id = 3;
        $this->assertNull($this->pager->getObjectByCursor(4));
        $this->assertEquals(3, $this->pager->getCursor());
    }

    public function testGetFirstPage(): void
    {
        $this->assertEquals(1, $this->pager->getFirstPage());
    }

    public function testGetNextPage(): void
    {
        $this->assertEquals(1, $this->pager->getNextPage());

        $this->pager->setPage(3);
        $this->callMethod($this->pager, 'setLastPage', [20]);
        $this->assertEquals(4, $this->pager->getNextPage());

        $this->pager->setPage(21);
        $this->assertEquals(20, $this->pager->getNextPage());
    }

    public function testGetPreviousPage(): void
    {
        $this->assertEquals(1, $this->pager->getPreviousPage());

        $this->pager->setPage(3);
        $this->assertEquals(2, $this->pager->getPreviousPage());

        $this->pager->setPage(21);
        $this->assertEquals(20, $this->pager->getPreviousPage());
    }

    public function testGetFirstIndice(): void
    {
        $this->assertEquals(1, $this->pager->getFirstIndice());

        $this->pager->setMaxPerPage(0);
        $this->pager->setPage(0);
        $this->assertEquals(1, $this->pager->getFirstIndice());

        $this->pager->setPage(2);
        $this->pager->setMaxPerPage(10);
        $this->assertEquals(11, $this->pager->getFirstIndice());

        $this->pager->setPage(4);
        $this->pager->setMaxPerPage(7);
        $this->assertEquals(22, $this->pager->getFirstIndice());
    }

    public function testGetLastIndice(): void
    {
        $this->assertEquals(0, $this->pager->getLastIndice());

        $this->pager->setMaxPerPage(0);
        $this->pager->setPage(0);
        $this->assertEquals(0, $this->pager->getLastIndice());

        $this->callMethod($this->pager, 'setNbResults', [100]);

        $this->assertEquals(100, $this->pager->getLastIndice());

        $this->pager->setPage(2);
        $this->assertEquals(0, $this->pager->getLastIndice());

        $this->pager->setMaxPerPage(10);
        $this->assertEquals(20, $this->pager->getLastIndice());

        $this->pager->setPage(11);
        $this->assertEquals(100, $this->pager->getLastIndice());
    }

    public function testGetNext(): void
    {
        $this->assertNull($this->pager->getNext());

        $object1 = new \stdClass();
        $object1->foo = 'bar1';

        $object2 = new \stdClass();
        $object2->foo = 'bar2';

        $object3 = new \stdClass();
        $object3->foo = 'bar3';

        $this->callMethod($this->pager, 'setNbResults', [3]);

        $query = $this->createMock('Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface');

        $query->expects($this->any())
            ->method('setFirstResult')
            ->will($this->returnValue($query));

        $query->expects($this->any())
            ->method('setMaxResults')
            ->will($this->returnValue($query));

        $id = 0;
        $query->expects($this->any())
            ->method('execute')
            ->will($this->returnCallback(function () use (&$id, $object1, $object2, $object3) {
                switch ($id) {
                    case 0:
                        return [$object1];

                        break;

                    case 1:
                        return [$object2];

                        break;

                    case 2:
                        return [$object3];

                        break;
                }
            }));

        $this->pager->setQuery($query);

        $this->pager->setCursor(1);
        $this->assertEquals($object1, $this->pager->getCurrent());

        ++$id;
        $this->assertEquals($object2, $this->pager->getNext());

        ++$id;
        $this->assertEquals($object3, $this->pager->getNext());

        ++$id;
        $this->assertNull($this->pager->getNext());
    }

    public function testGetPrevious(): void
    {
        $this->assertNull($this->pager->getPrevious());

        $object1 = new \stdClass();
        $object1->foo = 'bar1';

        $object2 = new \stdClass();
        $object2->foo = 'bar2';

        $object3 = new \stdClass();
        $object3->foo = 'bar3';

        $this->callMethod($this->pager, 'setNbResults', [3]);

        $query = $this->createMock('Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface');

        $query->expects($this->any())
            ->method('setFirstResult')
            ->will($this->returnValue($query));

        $query->expects($this->any())
            ->method('setMaxResults')
            ->will($this->returnValue($query));

        $id = 2;
        $query->expects($this->any())
            ->method('execute')
            ->will($this->returnCallback(function () use (&$id, $object1, $object2, $object3) {
                switch ($id) {
                    case 0:
                        return [$object1];

                        break;

                    case 1:
                        return [$object2];

                        break;

                    case 2:
                        return [$object3];

                        break;
                }
            }));

        $this->pager->setQuery($query);

        $this->pager->setCursor(2);
        $this->assertEquals($object3, $this->pager->getCurrent());

        --$id;
        $this->assertEquals($object2, $this->pager->getPrevious());

        --$id;
        $this->assertEquals($object1, $this->pager->getPrevious());

        --$id;
        $this->assertNull($this->pager->getPrevious());
    }

    public function testSerialize(): void
    {
        $pagerClone = clone $this->pager;
        $data = $this->pager->serialize();
        $this->assertNotEmpty($data);

        $this->pager->setPage(12);
        $this->pager->setMaxPerPage(4);
        $this->pager->setMaxPageLinks(6);

        $this->pager->unserialize($data);
        $this->assertEquals($pagerClone, $this->pager);
    }

    public function testUnserialize(): void
    {
        $serialized = [
            'page' => 6,
            'maxPerPage' => 7,
            'maxPageLinks' => 5,
            'lastPage' => 4,
            'nbResults' => 30,
            'cursor' => 3,
            'parameters' => ['foo' => 'bar'],
            'currentMaxLink' => 2,
            'maxRecordLimit' => 22,
            'countColumn' => ['idx'],
        ];

        $this->pager->expects($this->any())
            ->method('getResults')
            ->will($this->returnValue([]));
        $this->pager->current();

        $this->pager->unserialize(serialize($serialized));

        $this->assertEquals(7, $this->pager->getMaxPerPage());
        $this->assertEquals(6, $this->pager->getPage());
        $this->assertEquals(5, $this->pager->getMaxPageLinks());
        $this->assertEquals(4, $this->pager->getLastPage());
        $this->assertEquals(['idx'], $this->pager->getCountColumn());
        $this->assertEquals(30, $this->pager->getNbResults());
        $this->assertEquals(3, $this->pager->getCursor());
        $this->assertEquals(['foo' => 'bar'], $this->pager->getParameters());
        $this->assertEquals(2, $this->pager->getCurrentMaxLink());
        $this->assertEquals(22, $this->pager->getMaxRecordLimit());
        $this->assertNull($this->pager->getQuery());
    }

    protected function callMethod($obj, $name, array $args = [])
    {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($obj, $args);
    }
}
