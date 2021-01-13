<?php

namespace Nebkam\OdmSearchParam\Tests;

use Doctrine\Common\Annotations\AnnotationReader;
use Nebkam\OdmSearchParam\Tests\Documents\SearchableDocument;

class SearchFilterTest extends BaseTest
	{
	public function testStringType(): void
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
	}
