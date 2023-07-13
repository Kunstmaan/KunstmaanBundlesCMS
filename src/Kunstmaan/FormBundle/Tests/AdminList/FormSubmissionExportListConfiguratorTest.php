<?php

namespace Kunstmaan\FormBundle\Tests\AdminList;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\FormBundle\AdminList\FormSubmissionExportListConfigurator;
use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\BooleanFormSubmissionField;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Translator;

class FakeFormSubmission extends FormSubmission
{
    public function setFields(ArrayCollection $fields)
    {
        $this->fields = $fields;
    }
}

class FormSubmissionExportListConfiguratorTest extends TestCase
{
    /**
     * @var FormSubmissionExportListConfigurator
     */
    protected $object;

    protected function setUp(): void
    {
        $em = $this->getMockedEntityManager();
        $node = new Node();
        $node->setId(666);
        $nt = new NodeTranslation();
        $nt->setNode($node);
        $translator = new Translator('nl');
        $this->object = new FormSubmissionExportListConfigurator($em, $nt, $translator);
    }

    /**
     * @return EntityManager
     */
    protected function getMockedEntityManager()
    {
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $field = new BooleanFormSubmissionField();
        $field->setFieldName('check');
        $field->setValue(true);

        $sub = new FakeFormSubmission();
        $sub->setFields(new ArrayCollection([
            $field,
        ]));

        $submissions = [
            [$sub],
            [new FormSubmission()],
            [new FormSubmission()],
        ];
        $query = $this->getMockBuilder(AbstractQuery::class)
            ->disableOriginalConstructor()
            ->getMock();

        $query
            ->method('iterate')
            ->willReturn($submissions);

        $methods = [
            'select', 'from', 'innerJoin', 'andWhere', 'setParameter', 'addOrderBy',
        ];
        foreach ($methods as $method) {
            $queryBuilder
                ->method($method)
                ->willReturn($queryBuilder);
        }

        $queryBuilder
            ->method('getQuery')
            ->willReturn($query);

        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getQuoteStrategy')->willReturn(null);

        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->willReturn(null);
        $repository->method('findBy')->willReturn(null);
        $repository->method('findOneBy')->willReturn(null);

        $emMock = $this->createMock(EntityManager::class);
        $emMock->method('getRepository')->willReturn($repository);
        $emMock->method('getClassMetaData')->willReturn((object) ['name' => 'aClass']);
        $emMock->method('getConfiguration')->willReturn($configuration);
        $emMock->method('clear')->willReturn(null);
        $emMock->method('createQueryBuilder')->willReturn($queryBuilder);
        $emMock->method('persist')->willReturn(null);
        $emMock->method('flush')->willReturn(null);

        return $emMock;
    }

    public function testGetStringValue()
    {
        $this->assertNull($this->object->buildFilters());
        $this->assertSame('', $this->object->getStringValue([], 'fail'));
        $this->assertSame('pass', $this->object->getStringValue(['test' => 'pass'], 'test'));
    }

    public function testBuildExportFields()
    {
        $this->object->buildExportFields();
        $this->assertCount(3, $this->object->getExportFields());
        $this->object->addExportField('abc', 'def');
        $this->assertCount(4, $this->object->getExportFields());
    }

    public function testBuildIterator()
    {
        $this->object->buildIterator();
        $filters = $this->object->getIterator();
        $this->assertInstanceOf(\ArrayIterator::class, $filters);
        $first = $filters->current();
        $this->assertCount(1, $first);
        $first = $first[0];
        $this->assertArrayHasKey('check', $first);
        $this->assertSame('true', $first['check']);
    }
}
