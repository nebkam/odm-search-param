<?php

namespace Nebkam\OdmSearchParam\Tests;

use DateTime;
use Nebkam\OdmSearchParam\SearchParam;
use Nebkam\OdmSearchParam\SearchParamDirection;
use Nebkam\OdmSearchParam\SearchParamType;

class SearchFilter
	{
	#[SearchParam(type: SearchParamType::String)]
	public ?string $stringProperty = null;

	#[SearchParam(type: SearchParamType::Int)]
	public ?string $intProperty = null;

	#[SearchParam(type: SearchParamType::Bool)]
	public ?bool $boolProperty = null;

	#[SearchParam(type: SearchParamType::VirtualBool)]
	public ?bool $virtualBoolProperty = null;

	#[SearchParam(type: SearchParamType::Exists)]
	public ?string $existsProperty = null;

	#[SearchParam(type: SearchParamType::Range, direction: SearchParamDirection::From)]
	public ?DateTime $rangeDateTimeFromProperty = null;

	#[SearchParam(type: SearchParamType::RangeInt, direction: SearchParamDirection::From)]
	public ?string $rangeIntFromProperty = null;

	#[SearchParam(type: SearchParamType::RangeInt, direction: SearchParamDirection::To)]
	public ?string $rangeIntToProperty = null;

	#[SearchParam(type: SearchParamType::RangeFloat, direction: SearchParamDirection::From)]
	public ?string $rangeFloatFromProperty = null;

	#[SearchParam(type: SearchParamType::RangeFloat, direction: SearchParamDirection::To)]
	public ?string $rangeFloatToProperty = null;

	#[SearchParam(type: SearchParamType::StringArray)]
	public ?array $stringArrayProperty = null;

	#[SearchParam(type: SearchParamType::StringArray, invert: true)]
	public ?array $stringArrayInvertedProperty = null;

	#[SearchParam(type: SearchParamType::IntArray)]
	public ?array $integerArrayProperty = null;

	#[SearchParam(type: SearchParamType::IntArray, invert: true)]
	public ?array $integerArrayInvertedProperty = null;

	#[SearchParam(type: SearchParamType::String, field: 'alias')]
	public ?string $aliasProperty = null;

	#[SearchParam(callable: [SearchFilterDependency::class, 'setFoo'])]
	public ?int $callableProperty = null;
	}
