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
	 * @SearchParam(type="int")
	 * @var string|null
	 */
	public ?string $intProperty = null;

	/**
	 * @SearchParam(type="bool")
	 * @var bool|null
	 */
	public ?bool $boolProperty = null;

	/**
	 * @SearchParam(type="virtual_bool")
	 * @var bool|null
	 */
	public ?bool $virtualBoolProperty = null;

	/**
	 * @SearchParam(type="exists")
	 * @var string|null
	 */
	public ?string $existsProperty = null;

	/**
	 * @SearchParam(type="range_int", direction="from")
	 * @var string|null
	 */
	public ?string $rangeIntFromProperty = null;

	/**
	 * @SearchParam(type="range_int", direction="to")
	 * @var string|null
	 */
	public ?string $rangeIntToProperty = null;

	/**
	 * @SearchParam(type="range_float", direction="from")
	 * @var string|null
	 */
	public ?string $rangeFloatFromProperty = null;

	/**
	 * @SearchParam(type="range_float", direction="to")
	 * @var string|null
	 */
	public ?string $rangeFloatToProperty = null;

	/**
	 * @SearchParam(type="string_array")
	 * @var string[]|null
	 */
	public ?array $stringArrayProperty = null;

	/**
	 * @SearchParam(type="string_array", invert=true)
	 * @var string[]|null
	 */
	public ?array $stringArrayInvertedProperty = null;

	/**
	 * @SearchParam(type="int_array")
	 * @var int[]|null
	 */
	public ?array $integerArrayProperty = null;

	/**
	 * @SearchParam(type="int_array", invert=true)
	 * @var int[]|null
	 */
	public ?array $integerArrayInvertedProperty = null;

	/**
	 * @SearchParam(type="string", field="alias")
	 * @var string|null
	 */
	public ?string $aliasProperty = null;

	/**
	 * @SearchParam(callable={"Nebkam\OdmSearchParam\Tests\SearchFilterDependency", "setFoo"})
	 * @var int|null
	 */
	public ?int $callableProperty = null;
	}
