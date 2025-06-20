<?php

namespace Nebkam\OdmSearchParam\Tests;

use DateTime;
use MongoDB\BSON\Regex;
use MongoDB\BSON\UTCDateTime;
use Nebkam\OdmSearchParam\SearchParamParser;
use Nebkam\OdmSearchParam\Tests\Documents\SearchableDocument;
use ReflectionException;

class SearchParamTest extends BaseTestCase
	{
	/**
	 * @throws ReflectionException
	 */
	public function testEmptyFilter(): void
		{
		$filter       = new SearchFilter();
		$queryBuilder = self::createTestQueryBuilder(SearchableDocument::class);

		SearchParamParser::parse($filter, $queryBuilder);

		self::assertBuiltQueryEquals($queryBuilder, []);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testFieldAlias(): void
		{
		$filter                = new SearchFilter();
		$filter->aliasProperty = 'foo';
		$queryBuilder          = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage            = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertBuiltQueryEquals($queryBuilder, ['alias' => 'foo']);
		self::assertBuiltMatchStageEquals($matchStage, ['alias' => 'foo']);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testBool(): void
		{
		$filter               = new SearchFilter();
		$filter->boolProperty = true;
		$queryBuilder         = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage           = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertBuiltQueryEquals($queryBuilder, ['boolProperty' => true]);
		self::assertBuiltMatchStageEquals($matchStage, ['boolProperty' => true]);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testBoolFalse(): void
		{
		$filter               = new SearchFilter();
		$filter->boolProperty = false;
		$queryBuilder         = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage           = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertBuiltQueryEquals($queryBuilder, ['boolProperty' => false]);
		self::assertBuiltMatchStageEquals($matchStage, ['boolProperty' => false]);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testCallable(): void
		{
		$filter                   = new SearchFilter();
		$filter->callableProperty = 4;
		$queryBuilder             = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage               = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertBuiltQueryEquals($queryBuilder, ['foo' => 4]);
		self::assertBuiltMatchStageEquals($matchStage, ['foo' => 4]);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testExists(): void
		{
		$filter                 = new SearchFilter();
		$filter->existsProperty = '1';
		$queryBuilder           = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage             = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertBuiltQueryEquals($queryBuilder, ['existsProperty' => ['$exists' => true]]);
		self::assertBuiltMatchStageEquals($matchStage, ['existsProperty' => ['$exists' => true]]);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testExistsInverted(): void
		{
		$filter                         = new SearchFilter();
		$filter->existsInvertedProperty = '1';
		$queryBuilder                   = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage                     = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertBuiltQueryEquals($queryBuilder, ['existsInvertedProperty' => ['$exists' => false]]);
		self::assertBuiltMatchStageEquals($matchStage, ['existsInvertedProperty' => ['$exists' => false]]);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testInt(): void
		{
		$filter              = new SearchFilter();
		$filter->intProperty = '1';
		$queryBuilder        = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage          = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertBuiltQueryEquals($queryBuilder, ['intProperty' => 1]);
		self::assertBuiltMatchStageEquals($matchStage, ['intProperty' => 1]);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testIntEnum(): void
		{
		$filter                  = new SearchFilter();
		$filter->intEnumProperty = IntEnum::FOO;
		$queryBuilder            = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage              = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertBuiltQueryEquals($queryBuilder, ['intEnumProperty' => IntEnum::FOO->value]);
		self::assertBuiltMatchStageEquals($matchStage, ['intEnumProperty' => IntEnum::FOO->value]);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testIntArray(): void
		{
		$filter                   = new SearchFilter();
		$filter->intArrayProperty = ['1', '2'];
		$queryBuilder             = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage               = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		$builtQuery = self::assertBuiltQueryEquals($queryBuilder, ['intArrayProperty' => ['$in' => [1, 2]]]);
		// Test casting
		self::assertIsInt($builtQuery['intArrayProperty']['$in'][0]);
		self::assertIsInt($builtQuery['intArrayProperty']['$in'][1]);
		$matchExpression = self::assertBuiltMatchStageEquals($matchStage, ['intArrayProperty' => ['$in' => [1, 2]]]);
		// Test casting
		self::assertIsInt($matchExpression['intArrayProperty']['$in'][0]);
		self::assertIsInt($matchExpression['intArrayProperty']['$in'][1]);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testIntEnumArray(): void
		{
		$filter                       = new SearchFilter();
		$filter->intEnumArrayProperty = [IntEnum::FOO, IntEnum::BAR];
		$queryBuilder                 = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage                   = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		$builtQuery = self::assertBuiltQueryEquals($queryBuilder, ['intEnumArrayProperty' => ['$in' => [IntEnum::FOO->value, IntEnum::BAR->value]]]);
		// Test casting
		self::assertIsInt($builtQuery['intEnumArrayProperty']['$in'][0]);
		self::assertIsInt($builtQuery['intEnumArrayProperty']['$in'][1]);
		$matchExpression = self::assertBuiltMatchStageEquals($matchStage, ['intEnumArrayProperty' => ['$in' => [IntEnum::FOO->value, IntEnum::BAR->value]]]);
		// Test casting
		self::assertIsInt($matchExpression['intEnumArrayProperty']['$in'][0]);
		self::assertIsInt($matchExpression['intEnumArrayProperty']['$in'][1]);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testIntArrayInverted(): void
		{
		$filter                           = new SearchFilter();
		$filter->intArrayInvertedProperty = ['3', '4'];
		$queryBuilder                     = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage                       = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		$builtQuery = self::assertBuiltQueryEquals($queryBuilder, ['intArrayInvertedProperty' => ['$nin' => [3, 4]]]);
		// Test casting
		self::assertIsInt($builtQuery['intArrayInvertedProperty']['$nin'][0]);
		self::assertIsInt($builtQuery['intArrayInvertedProperty']['$nin'][1]);
		$matchExpression = self::assertBuiltMatchStageEquals($matchStage, ['intArrayInvertedProperty' => ['$nin' => [3, 4]]]);
		// Test casting
		self::assertIsInt($matchExpression['intArrayInvertedProperty']['$nin'][0]);
		self::assertIsInt($matchExpression['intArrayInvertedProperty']['$nin'][1]);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testDateTimeRangeFrom(): void
		{
		$filter                            = new SearchFilter();
		$filter->rangeDateTimeFromProperty = new DateTime('-1 day');
		$queryBuilder                      = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage                        = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		$queryDebug = $queryBuilder->getQuery()->debug();
		self::assertArrayHasKey('$gte', $queryDebug['query']['rangeDateTimeFromProperty']);
		self::assertInstanceOf(UTCDateTime::class, $queryDebug['query']['rangeDateTimeFromProperty']['$gte']);
		$matchExpression = $matchStage->getExpression();
		self::assertArrayHasKey('$gte', $matchExpression['$match']['rangeDateTimeFromProperty']);
		self::assertInstanceOf(UTCDateTime::class, $matchExpression['$match']['rangeDateTimeFromProperty']['$gte']);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testRangeFloatFrom(): void
		{
		$filter                         = new SearchFilter();
		$filter->rangeFloatFromProperty = '1.1';
		$queryBuilder                   = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage                     = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertBuiltQueryEquals($queryBuilder, ['rangeFloatFromProperty' => ['$gte' => 1.1]]);
		self::assertBuiltMatchStageEquals($matchStage, ['rangeFloatFromProperty' => ['$gte' => 1.1]]);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testRangeFloatTo(): void
		{
		$filter                       = new SearchFilter();
		$filter->rangeFloatToProperty = '5.0';
		$queryBuilder                 = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage                   = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertBuiltQueryEquals($queryBuilder, ['rangeFloatToProperty' => ['$lte' => 5.0]]);
		self::assertBuiltMatchStageEquals($matchStage, ['rangeFloatToProperty' => ['$lte' => 5.0]]);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testRangeIntFrom(): void
		{
		$filter                       = new SearchFilter();
		$filter->rangeIntFromProperty = '10';
		$queryBuilder                 = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage                   = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertBuiltQueryEquals($queryBuilder, ['rangeIntFromProperty' => ['$gte' => 10]]);
		self::assertBuiltMatchStageEquals($matchStage, ['rangeIntFromProperty' => ['$gte' => 10]]);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testRangeIntTo(): void
		{
		$filter                     = new SearchFilter();
		$filter->rangeIntToProperty = '50';
		$queryBuilder               = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage                 = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertBuiltQueryEquals($queryBuilder, ['rangeIntToProperty' => ['$lte' => 50]]);
		self::assertBuiltMatchStageEquals($matchStage, ['rangeIntToProperty' => ['$lte' => 50]]);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testRangeIntEnumFrom(): void
		{
		$filter                           = new SearchFilter();
		$filter->rangeIntEnumFromProperty = IntEnum::BAR;
		$queryBuilder                     = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage                       = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertBuiltQueryEquals($queryBuilder, ['rangeIntEnumFromProperty' => ['$gte' => IntEnum::BAR->value]]);
		self::assertBuiltMatchStageEquals($matchStage, ['rangeIntEnumFromProperty' => ['$gte' => IntEnum::BAR->value]]);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testRangeEnumIntTo(): void
		{
		$filter                         = new SearchFilter();
		$filter->rangeIntEnumToProperty = IntEnum::FOO;
		$queryBuilder                   = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage                     = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertBuiltQueryEquals($queryBuilder, ['rangeIntEnumToProperty' => ['$lte' => IntEnum::FOO->value]]);
		self::assertBuiltMatchStageEquals($matchStage, ['rangeIntEnumToProperty' => ['$lte' => IntEnum::FOO->value]]);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testRegex(): void
		{
		$filter                = new SearchFilter();
		$filter->regexProperty = 'foo';
		$queryBuilder          = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage            = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertEquals(new Regex('foo', 'i'), $queryBuilder->getQuery()->debug()['query']['regexProperty']);
		self::assertEquals(new Regex('foo', 'i'), $matchStage->getExpression()['$match']['regexProperty']);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testString(): void
		{
		$filter                 = new SearchFilter();
		$filter->stringProperty = 'foo';
		$queryBuilder           = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage             = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertBuiltQueryEquals($queryBuilder, ['stringProperty' => 'foo']);
		self::assertBuiltMatchStageEquals($matchStage, ['stringProperty' => 'foo']);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testStringArray(): void
		{
		$filter                      = new SearchFilter();
		$filter->stringArrayProperty = ['foo', 'bar'];
		$queryBuilder                = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage                  = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertBuiltQueryEquals($queryBuilder, ['stringArrayProperty' => ['$in' => ['foo', 'bar']]]);
		self::assertBuiltMatchStageEquals($matchStage, ['stringArrayProperty' => ['$in' => ['foo', 'bar']]]);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testStringArrayInverted(): void
		{
		$filter                              = new SearchFilter();
		$filter->stringArrayInvertedProperty = ['foo', 'bar'];
		$queryBuilder                        = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage                          = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertBuiltQueryEquals($queryBuilder, ['stringArrayInvertedProperty' => ['$nin' => ['foo', 'bar']]]);
		self::assertBuiltMatchStageEquals($matchStage, ['stringArrayInvertedProperty' => ['$nin' => ['foo', 'bar']]]);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testStringEnum(): void
		{
		$filter                     = new SearchFilter();
		$filter->stringEnumProperty = StringEnum::FOO;
		$queryBuilder               = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage                 = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertBuiltQueryEquals($queryBuilder, ['stringEnumProperty' => 'foo']);
		self::assertBuiltMatchStageEquals($matchStage, ['stringEnumProperty' => 'foo']);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testStringEnumArray(): void
		{
		$filter                          = new SearchFilter();
		$filter->stringEnumArrayProperty = [StringEnum::FOO, StringEnum::BAR];
		$queryBuilder                    = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage                      = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertBuiltQueryEquals($queryBuilder, ['stringEnumArrayProperty' => ['$in' => [StringEnum::FOO->value, StringEnum::BAR->value]]]);
		self::assertBuiltMatchStageEquals($matchStage, ['stringEnumArrayProperty' => ['$in' => [StringEnum::FOO->value, StringEnum::BAR->value]]]);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testVirtualBool(): void
		{
		$filter                      = new SearchFilter();
		$filter->virtualBoolProperty = true;
		$queryBuilder                = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage                  = self::createTestAggregationBuilder(SearchableDocument::class)->match();

		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertBuiltQueryEquals($queryBuilder, ['virtualBoolProperty' => ['$gte' => 1]]);
		self::assertBuiltMatchStageEquals($matchStage, ['virtualBoolProperty' => ['$gte' => 1]]);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testGeoWithingBox(): void
		{
		$filter              = new SearchFilter();
		$filter->mapBoundary = [20.441587, 44.806884, 20.482464, 44.821999];
		$queryBuilder        = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage          = self::createTestAggregationBuilder(SearchableDocument::class)->match();
		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertBuiltQueryEquals($queryBuilder, ['mapBoundary' => ['$geoWithin' => ['$box' => [[20.441587, 44.806884], [20.482464, 44.821999]]]]]);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testGeoWithingPolygonBox(): void
		{
		$filter                 = new SearchFilter();
		$filter->boxCoordinates = [20.441587, 44.806884, 20.482464, 44.821999];
		$queryBuilder           = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage             = self::createTestAggregationBuilder(SearchableDocument::class)->match();
		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertBuiltQueryEquals($queryBuilder, [
			'boxCoordinates' => [
				'$geoWithin' => [
					'$geometry' => [
						'type' => 'Polygon',
						'coordinates' => [[
							[20.441587, 44.806884],
							[20.482464, 44.806884],
							[20.482464, 44.821999],
							[20.441587, 44.821999],
							[20.441587, 44.806884]
						]]
					]
				]
			]
		]);
		self::assertBuiltMatchStageEquals($matchStage, [
			'boxCoordinates' => [
				'$geoWithin' => [
					'$geometry' => [
						'type' => 'Polygon',
						'coordinates' => [[
							[20.441587, 44.806884],
							[20.482464, 44.806884],
							[20.482464, 44.821999],
							[20.441587, 44.821999],
							[20.441587, 44.806884]
						]]
					]
				]
			]
		]);
		}

	/**
	 * @throws ReflectionException
	 */
	public function testWrongValueTypeForWithingBoundaryBox(): void
		{
		$filter                   = new SearchFilter();
		$filter->wrongMapBoundary = '20.441587, 44.806884, 20.482464, 44.821999';
		$queryBuilder             = self::createTestQueryBuilder(SearchableDocument::class);
		$matchStage               = self::createTestAggregationBuilder(SearchableDocument::class)->match();
		SearchParamParser::parse($filter, $queryBuilder);
		SearchParamParser::parse($filter, $matchStage);
		self::assertBuiltQueryEquals($queryBuilder, []);
		}
	}
