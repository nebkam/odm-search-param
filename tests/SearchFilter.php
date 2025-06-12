<?php

namespace Nebkam\OdmSearchParam\Tests;

use DateTime;
use Nebkam\OdmSearchParam\SearchParam;
use Nebkam\OdmSearchParam\SearchParamDirection;
use Nebkam\OdmSearchParam\SearchParamType;

class SearchFilter
	{
	#[SearchParam(type: SearchParamType::String, field: 'alias')]
	public ?string $aliasProperty = null;

	#[SearchParam(type: SearchParamType::Bool)]
	public ?bool $boolProperty = null;

	#[SearchParam(type: SearchParamType::Callable, callable: [SearchFilterDependency::class, 'setFoo'])]
	public ?int $callableProperty = null;

	#[SearchParam(type: SearchParamType::Exists)]
	public ?string $existsProperty = null;

	#[SearchParam(type: SearchParamType::Exists, invert: true)]
	public ?string $existsInvertedProperty = null;

	#[SearchParam(type: SearchParamType::Int)]
	public ?string $intProperty = null;

	#[SearchParam(type: SearchParamType::IntArray)]
	public ?array $intArrayProperty = null;

	#[SearchParam(type: SearchParamType::IntEnum)]
	public ?IntEnum $intEnumProperty = null;

	/**
	 * @var IntEnum[]|null
	 */
	#[SearchParam(type: SearchParamType::IntEnumArray)]
	public ?array $intEnumArrayProperty = null;

	#[SearchParam(type: SearchParamType::IntArray, invert: true)]
	public ?array $intArrayInvertedProperty = null;

	#[SearchParam(type: SearchParamType::Range, direction: SearchParamDirection::From)]
	public ?DateTime $rangeDateTimeFromProperty = null;

	#[SearchParam(type: SearchParamType::RangeFloat, direction: SearchParamDirection::From)]
	public ?string $rangeFloatFromProperty = null;

	#[SearchParam(type: SearchParamType::RangeFloat, direction: SearchParamDirection::To)]
	public ?string $rangeFloatToProperty = null;

	#[SearchParam(type: SearchParamType::RangeInt, direction: SearchParamDirection::From)]
	public ?string $rangeIntFromProperty = null;

	#[SearchParam(type: SearchParamType::RangeInt, direction: SearchParamDirection::To)]
	public ?string $rangeIntToProperty = null;

	#[SearchParam(type: SearchParamType::RangeIntEnum, direction: SearchParamDirection::From)]
	public ?IntEnum $rangeIntEnumFromProperty = null;

	#[SearchParam(type: SearchParamType::RangeIntEnum, direction: SearchParamDirection::To)]
	public ?IntEnum $rangeIntEnumToProperty = null;

	#[SearchParam(type: SearchParamType::Regex, flags: 'i')]
	public ?string $regexProperty = null;

	#[SearchParam(type: SearchParamType::String)]
	public ?string $stringProperty = null;

	#[SearchParam(type: SearchParamType::StringArray)]
	public ?array $stringArrayProperty = null;

	#[SearchParam(type: SearchParamType::StringArray, invert: true)]
	public ?array $stringArrayInvertedProperty = null;

	#[SearchParam(type: SearchParamType::StringEnum)]
	public ?StringEnum $stringEnumProperty = null;

	/**
	 * @var StringEnum[]|null
	 */
	#[SearchParam(type: SearchParamType::StringEnumArray)]
	public ?array $stringEnumArrayProperty = null;

	#[SearchParam(type: SearchParamType::VirtualBool)]
	public ?bool $virtualBoolProperty = null;

	/**
	 * @var float[]|null
	 */
	#[SearchParam(type: SearchParamType::WithinBox)]
	public ?array $mapBoundary = null;

	#[SearchParam(type: SearchParamType::WithinBox)]
	public ?string $wrongMapBoundary = null;
	}
