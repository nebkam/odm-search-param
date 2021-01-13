<?php

namespace Nebkam\OdmSearchParam\Tests;

use PHPUnit\Framework\TestCase;

class SearchFilterTest extends TestCase
	{
	public function testFoo(): void
		{
		$filter = new SearchFilter();
		$filter->stringProperty = 'foo';
		}
	}
