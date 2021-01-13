<?php

namespace Nebkam\OdmSearchParam\Tests;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ODM\MongoDB\Query\Builder;
use Nebkam\OdmSearchParam\Tests\Documents\SearchableDocument;

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

	public function testStringArray(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->stringArrayProperty = ['foo', 'bar'];
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['stringArrayProperty' => ['$in' => ['foo', 'bar']]]);
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

	public function testFieldAlias(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->aliasProperty = 'foo';
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['alias' => 'foo']);
		}

	private static function assertBuiltQueryEquals(Builder $builder, array $query): array
		{
		$debug = $builder->getQuery()->debug();
		self::assertArrayHasKey('query', $debug);
		self::assertIsArray($debug['query']);
		self::assertEquals($query, $debug['query']);

		return $debug['query'];
		}
	}
