<?php

namespace Nebkam\OdmSearchParam\Tests;

use DateTime;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ODM\MongoDB\Query\Builder;
use MongoDB\BSON\UTCDateTime;
use Nebkam\OdmSearchParam\Tests\Documents\SearchableDocument;
use function PHPUnit\Framework\assertInstanceOf;

class SearchFilterTest extends BaseTest
	{
	public function testNullString(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, []);
		}

	public function testString(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->stringProperty = 'foo';
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['stringProperty' => 'foo']);
		}

	public function testInt(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->intProperty = '1';
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['intProperty' => 1]);
		}

	public function testBool(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->boolProperty = true;
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['boolProperty' => true]);
		}

	public function testBoolFalse(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->boolProperty = false;
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['boolProperty' => false]);
		}

	public function testVirtualBool(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->virtualBoolProperty = true;
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['virtualBoolProperty' => ['$gte' => 1]]);
		}

	public function testExists(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->existsProperty = '1';
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['existsProperty' => ['$exists' => true]]);
		}

	public function testDateTimeRangeFrom(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->rangeDateTimeFromProperty = new DateTime('-1 day');
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());
		$debug = $queryBuilder->getQuery()->debug();
		self::assertArrayHasKey('$gte', $debug['query']['rangeDateTimeFromProperty']);
		assertInstanceOf(UTCDateTime::class, $debug['query']['rangeDateTimeFromProperty']['$gte']);
		}

	public function testRangeIntFrom(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->rangeIntFromProperty = '10';
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['rangeIntFromProperty' => ['$gte' => 10]]);
		}

	public function testRangeIntTo(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->rangeIntToProperty = '50';
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['rangeIntToProperty' => ['$lte' => 50]]);
		}

	public function testRangeFloatFrom(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->rangeFloatFromProperty = '1.1';
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['rangeFloatFromProperty' => ['$gte' => 1.1]]);
		}

	public function testRangeFloatTo(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->rangeFloatToProperty = '5.0';
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['rangeFloatToProperty' => ['$lte' => 5.0]]);
		}

	public function testStringArray(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->stringArrayProperty = ['foo', 'bar'];
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['stringArrayProperty' => ['$in' => ['foo', 'bar']]]);
		}

	public function testStringArrayInverted(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->stringArrayInvertedProperty = ['foo', 'bar'];
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['stringArrayInvertedProperty' => ['$nin' => ['foo', 'bar']]]);
		}

	public function testIntegerArray(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->integerArrayProperty = ['1', '2'];
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());

		$builtQuery = self::assertBuiltQueryEquals($queryBuilder, ['integerArrayProperty' => ['$in' => [1, 2]]]);
		// Test casting
		self::assertIsInt($builtQuery['integerArrayProperty']['$in'][0]);
		self::assertIsInt($builtQuery['integerArrayProperty']['$in'][1]);
		}

	public function testIntegerArrayInverted(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->integerArrayInvertedProperty = ['3', '4'];
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());

		$builtQuery = self::assertBuiltQueryEquals($queryBuilder, ['integerArrayInvertedProperty' => ['$nin' => [3, 4]]]);
		// Test casting
		self::assertIsInt($builtQuery['integerArrayInvertedProperty']['$nin'][0]);
		self::assertIsInt($builtQuery['integerArrayInvertedProperty']['$nin'][1]);
		}

	public function testFieldAlias(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->aliasProperty = 'foo';
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['alias' => 'foo']);
		}

	public function testCallable(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->callableProperty = 4;
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['foo' => 4]);
		}

	private static function assertBuiltQueryEquals(Builder $builder, array $query): array
		{
		$debug = $builder->getQuery()->debug();
		self::assertArrayHasKey('query', $debug);
		self::assertIsArray($debug['query']);
		self::assertSame($query, $debug['query']);

		return $debug['query'];
		}
	}
