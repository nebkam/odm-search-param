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

	/**
	 * @SearchParam(type="string", field="alias")
	 * @var string|null
	 */
	public ?string $aliasProperty = null;

	/**
	 * @SearchParam(type="string_array")
	 * @var string[]|null
	 */
	public ?array $stringArrayProperty = null;

	/**
	 * @SearchParam(type="int_array")
	 * @var int[]|null
	 */
	public ?array $integerArrayProperty = null;
	}
