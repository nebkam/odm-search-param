<?php

namespace Nebkam\OdmSearchParam\Tests;

use DateTime;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ODM\MongoDB\Aggregation\Stage\MatchStage;
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
		$matchStage = $this->dm->createAggregationBuilder(SearchableDocument::class)->match();

		$filter = new SearchFilter();
		$filter->stringProperty = 'foo';

		$filter->parseSearchParam($queryBuilder, new AnnotationReader());
		$filter->parseSearchParam($matchStage, new AnnotationReader());
		self::assertBuiltQueryEquals($queryBuilder, ['stringProperty' => 'foo']);
		self::assertBuiltMatchStageEquals($matchStage, ['stringProperty' => 'foo']);
		}

	public function testInt(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);
		$matchStage = $this->dm->createAggregationBuilder(SearchableDocument::class)->match();

		$filter = new SearchFilter();
		$filter->intProperty = '1';
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());
		$filter->parseSearchParam($matchStage, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['intProperty' => 1]);
		self::assertBuiltMatchStageEquals($matchStage, ['intProperty' => 1]);
		}

	public function testBool(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);
		$matchStage = $this->dm->createAggregationBuilder(SearchableDocument::class)->match();

		$filter = new SearchFilter();
		$filter->boolProperty = true;
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());
		$filter->parseSearchParam($matchStage, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['boolProperty' => true]);
		self::assertBuiltMatchStageEquals($matchStage, ['boolProperty' => true]);
		}

	public function testBoolFalse(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);
		$matchStage = $this->dm->createAggregationBuilder(SearchableDocument::class)->match();

		$filter = new SearchFilter();
		$filter->boolProperty = false;
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());
		$filter->parseSearchParam($matchStage, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['boolProperty' => false]);
		self::assertBuiltMatchStageEquals($matchStage, ['boolProperty' => false]);
		}

	public function testVirtualBool(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);
		$matchStage = $this->dm->createAggregationBuilder(SearchableDocument::class)->match();

		$filter = new SearchFilter();
		$filter->virtualBoolProperty = true;
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());
		$filter->parseSearchParam($matchStage, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['virtualBoolProperty' => ['$gte' => 1]]);
		self::assertBuiltMatchStageEquals($matchStage, ['virtualBoolProperty' => ['$gte' => 1]]);
		}

	public function testExists(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);
		$matchStage = $this->dm->createAggregationBuilder(SearchableDocument::class)->match();

		$filter = new SearchFilter();
		$filter->existsProperty = '1';
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());
		$filter->parseSearchParam($matchStage, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['existsProperty' => ['$exists' => true]]);
		self::assertBuiltMatchStageEquals($matchStage, ['existsProperty' => ['$exists' => true]]);
		}

	public function testDateTimeRangeFrom(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);
		$matchStage = $this->dm->createAggregationBuilder(SearchableDocument::class)->match();

		$filter = new SearchFilter();
		$filter->rangeDateTimeFromProperty = new DateTime('-1 day');
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());
		$filter->parseSearchParam($matchStage, new AnnotationReader());
		$queryDebug = $queryBuilder->getQuery()->debug();
		self::assertArrayHasKey('$gte', $queryDebug['query']['rangeDateTimeFromProperty']);
		assertInstanceOf(UTCDateTime::class, $queryDebug['query']['rangeDateTimeFromProperty']['$gte']);
		$matchExpression = $matchStage->getExpression();
		self::assertArrayHasKey('$gte', $matchExpression['$match']['rangeDateTimeFromProperty']);
		assertInstanceOf(UTCDateTime::class, $matchExpression['$match']['rangeDateTimeFromProperty']['$gte']);
		}

	public function testRangeIntFrom(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);
		$matchStage = $this->dm->createAggregationBuilder(SearchableDocument::class)->match();

		$filter = new SearchFilter();
		$filter->rangeIntFromProperty = '10';
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());
		$filter->parseSearchParam($matchStage, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['rangeIntFromProperty' => ['$gte' => 10]]);
		self::assertBuiltMatchStageEquals($matchStage, ['rangeIntFromProperty' => ['$gte' => 10]]);
		}

	public function testRangeIntTo(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);
		$matchStage = $this->dm->createAggregationBuilder(SearchableDocument::class)->match();

		$filter = new SearchFilter();
		$filter->rangeIntToProperty = '50';
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());
		$filter->parseSearchParam($matchStage, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['rangeIntToProperty' => ['$lte' => 50]]);
		self::assertBuiltMatchStageEquals($matchStage, ['rangeIntToProperty' => ['$lte' => 50]]);
		}

	public function testRangeFloatFrom(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);
		$matchStage = $this->dm->createAggregationBuilder(SearchableDocument::class)->match();

		$filter = new SearchFilter();
		$filter->rangeFloatFromProperty = '1.1';
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());
		$filter->parseSearchParam($matchStage, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['rangeFloatFromProperty' => ['$gte' => 1.1]]);
		self::assertBuiltMatchStageEquals($matchStage, ['rangeFloatFromProperty' => ['$gte' => 1.1]]);
		}

	public function testRangeFloatTo(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);
		$matchStage = $this->dm->createAggregationBuilder(SearchableDocument::class)->match();

		$filter = new SearchFilter();
		$filter->rangeFloatToProperty = '5.0';
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());
		$filter->parseSearchParam($matchStage, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['rangeFloatToProperty' => ['$lte' => 5.0]]);
		self::assertBuiltMatchStageEquals($matchStage, ['rangeFloatToProperty' => ['$lte' => 5.0]]);
		}

	public function testStringArray(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);
		$matchStage = $this->dm->createAggregationBuilder(SearchableDocument::class)->match();

		$filter = new SearchFilter();
		$filter->stringArrayProperty = ['foo', 'bar'];
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());
		$filter->parseSearchParam($matchStage, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['stringArrayProperty' => ['$in' => ['foo', 'bar']]]);
		self::assertBuiltMatchStageEquals($matchStage, ['stringArrayProperty' => ['$in' => ['foo', 'bar']]]);
		}

	public function testStringArrayInverted(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);
		$matchStage = $this->dm->createAggregationBuilder(SearchableDocument::class)->match();

		$filter = new SearchFilter();
		$filter->stringArrayInvertedProperty = ['foo', 'bar'];
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());
		$filter->parseSearchParam($matchStage, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['stringArrayInvertedProperty' => ['$nin' => ['foo', 'bar']]]);
		self::assertBuiltMatchStageEquals($matchStage, ['stringArrayInvertedProperty' => ['$nin' => ['foo', 'bar']]]);
		}

	public function testIntegerArray(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);
		$matchStage = $this->dm->createAggregationBuilder(SearchableDocument::class)->match();

		$filter = new SearchFilter();
		$filter->integerArrayProperty = ['1', '2'];
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());
		$filter->parseSearchParam($matchStage, new AnnotationReader());

		$builtQuery = self::assertBuiltQueryEquals($queryBuilder, ['integerArrayProperty' => ['$in' => [1, 2]]]);
		// Test casting
		self::assertIsInt($builtQuery['integerArrayProperty']['$in'][0]);
		self::assertIsInt($builtQuery['integerArrayProperty']['$in'][1]);
		$matchExpression = self::assertBuiltMatchStageEquals($matchStage, ['integerArrayProperty' => ['$in' => [1, 2]]]);
		// Test casting
		self::assertIsInt($matchExpression['integerArrayProperty']['$in'][0]);
		self::assertIsInt($matchExpression['integerArrayProperty']['$in'][1]);
		}

	public function testIntegerArrayInverted(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);
		$matchStage = $this->dm->createAggregationBuilder(SearchableDocument::class)->match();

		$filter = new SearchFilter();
		$filter->integerArrayInvertedProperty = ['3', '4'];
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());
		$filter->parseSearchParam($matchStage, new AnnotationReader());

		$builtQuery = self::assertBuiltQueryEquals($queryBuilder, ['integerArrayInvertedProperty' => ['$nin' => [3, 4]]]);
		// Test casting
		self::assertIsInt($builtQuery['integerArrayInvertedProperty']['$nin'][0]);
		self::assertIsInt($builtQuery['integerArrayInvertedProperty']['$nin'][1]);
		$matchExpression = self::assertBuiltMatchStageEquals($matchStage, ['integerArrayInvertedProperty' => ['$nin' => [3, 4]]]);
		// Test casting
		self::assertIsInt($matchExpression['integerArrayInvertedProperty']['$nin'][0]);
		self::assertIsInt($matchExpression['integerArrayInvertedProperty']['$nin'][1]);
		}

	public function testFieldAlias(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);
		$matchStage = $this->dm->createAggregationBuilder(SearchableDocument::class)->match();

		$filter = new SearchFilter();
		$filter->aliasProperty = 'foo';
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());
		$filter->parseSearchParam($matchStage, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['alias' => 'foo']);
		self::assertBuiltMatchStageEquals($matchStage, ['alias' => 'foo']);
		}

	public function testCallable(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);
		$matchStage = $this->dm->createAggregationBuilder(SearchableDocument::class)->match();

		$filter = new SearchFilter();
		$filter->callableProperty = 4;
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());
		$filter->parseSearchParam($matchStage, new AnnotationReader());

		self::assertBuiltQueryEquals($queryBuilder, ['foo' => 4]);
		self::assertBuiltMatchStageEquals($matchStage, ['foo' => 4]);
		}

	private static function assertBuiltQueryEquals(Builder $builder, array $query): array
		{
		$debug = $builder->getQuery()->debug();
		self::assertArrayHasKey('query', $debug);
		self::assertIsArray($debug['query']);
		self::assertSame($query, $debug['query']);

		return $debug['query'];
		}

	/**
	 * @param MatchStage $matchStage
	 * @param array|object $query
	 * @return array|object
	 */
	private static function assertBuiltMatchStageEquals(MatchStage $matchStage, $query): array
		{
		$expression = $matchStage->getExpression();
		self::assertArrayHasKey('$match', $expression);
		self::assertIsArray($expression['$match']);
		self::assertSame($query, $expression['$match']);

		return $expression['$match'];
		}
	}
