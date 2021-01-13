<?php

namespace Nebkam\OdmSearchParam\Tests;

use Nebkam\OdmSearchParam\SearchParam;
use Nebkam\OdmSearchParam\SearchParamParseable;

class SearchFilter
	{
	use SearchParamParseable;

	/**
	 * @SearchParam(type="string")
	 * @var string|null
	 */
	public ?string $stringProperty = null;
	}
