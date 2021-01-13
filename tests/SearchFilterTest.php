<?php

namespace Nebkam\OdmSearchParam\Tests;

use Doctrine\Common\Annotations\AnnotationReader;
use Nebkam\OdmSearchParam\Tests\Documents\SearchableDocument;

class SearchFilterTest extends BaseTest
	{
	public function testNullString(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());
		$debug = $queryBuilder->getQuery()->debug();
		self::assertArrayHasKey('query', $debug);
		self::assertEmpty($debug['query']);
		}

	public function testString(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->stringProperty = 'foo';
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());
		$debug = $queryBuilder->getQuery()->debug();
		self::assertArrayHasKey('query', $debug);
		self::assertNotEmpty($debug['query']);
		self::assertEquals(['stringProperty' => 'foo'], $debug['query']);
		}

	public function testStringWithFieldAlias(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->aliasProperty = 'foo';
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());
		$debug = $queryBuilder->getQuery()->debug();
		self::assertArrayHasKey('query', $debug);
		self::assertNotEmpty($debug['query']);
		self::assertEquals(['alias' => 'foo'], $debug['query']);
		}

	public function testStringArray(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->stringArrayProperty = ['foo', 'bar'];
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());
		$debug = $queryBuilder->getQuery()->debug();
		self::assertArrayHasKey('query', $debug);
		self::assertNotEmpty($debug['query']);
		self::assertArrayHasKey('stringArrayProperty', $debug['query']);
		self::assertArrayHasKey('$in', $debug['query']['stringArrayProperty']);
		self::assertEquals(['foo', 'bar'], $debug['query']['stringArrayProperty']['$in']);
		}

	public function testIntegerArray(): void
		{
		$queryBuilder = $this->dm->createQueryBuilder(SearchableDocument::class);

		$filter = new SearchFilter();
		$filter->integerArrayProperty = ['1', '2'];
		$filter->parseSearchParam($queryBuilder, new AnnotationReader());
		$debug = $queryBuilder->getQuery()->debug();
		self::assertArrayHasKey('query', $debug);
		self::assertNotEmpty($debug['query']);
		self::assertArrayHasKey('integerArrayProperty', $debug['query']);
		self::assertArrayHasKey('$in', $debug['query']['integerArrayProperty']);
		self::assertEquals([1, 2], $debug['query']['integerArrayProperty']['$in']);
		// Test casting
		self::assertIsInt($debug['query']['integerArrayProperty']['$in'][0]);
		self::assertIsInt($debug['query']['integerArrayProperty']['$in'][1]);
		}
	}
