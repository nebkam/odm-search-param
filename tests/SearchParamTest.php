<?php

namespace Nebkam\OdmSearchParam\Tests;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Query\Builder;
use Nebkam\OdmSearchParam\SearchParamParser;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class SearchParamTest extends TestCase
	{
	/**
	 * @throws ReflectionException
	 */
	public function testTypeString(): void
		{
		$filter = new SearchFilter();
		$filter->stringProperty = 'foo';

		$parser = new SearchParamParser();
		$parser->parse($filter, $builder);
		}
	}
