<?php

namespace Nebkam\OdmSearchParam;

use Doctrine\ODM\MongoDB\Aggregation\Stage\MatchStage;
use Doctrine\ODM\MongoDB\Query\Builder;
use IntBackedEnum;
use ReflectionClass;
use ReflectionException;
use StringBackedEnum;

class SearchParamParser
	{
	/**
	 * @throws ReflectionException
	 */
	public function parse($filter, Builder|MatchStage $builder): void
		{
		$reflectionClass = new ReflectionClass($filter);
		foreach ($reflectionClass->getProperties() as $reflectionProperty)
			{
			$attributes = $reflectionProperty->getAttributes(SearchParam::class);
			if (!empty($attributes))
				{
				/** @var SearchParam $attribute */
				$attribute = $attributes[0]->newInstance();
				$field     = $attribute->field ?: $reflectionProperty->getName();
				$value     = $reflectionProperty->getValue($filter);
				if ($value !== null)
					{
					switch ($attribute->type)
						{
						case SearchParamType::Bool:
							if ($attribute->invert)
								{
								$builder->field($field)->notEqual((bool)$value);
								}
							else
								{
								$builder->field($field)->equals((bool)$value);
								}
							break;

						case SearchParamType::Exists:
							$builder->field($field)->exists((bool)$value);
							break;

						case SearchParamType::Int:
							if ($attribute->invert)
								{
								$builder->field($field)->notEqual((int)$value);
								}
							else
								{
								$builder->field($field)->equals((int)$value);
								}
							break;

						case SearchParamType::IntArray:
							if (count($value) > 0)
								{
								$intValues = array_map(static fn($item) => (int)$item, $value);
								if ($attribute->invert)
									{
									$builder->field($field)->notIn($intValues);
									}
								else
									{
									$builder->field($field)->in($intValues);
									}
								}
							break;

						case SearchParamType::IntEnum:
							/** @var IntBackedEnum $value */
							if ($attribute->invert)
								{
								$builder->field($field)->notEqual($value->value);
								}
							else
								{
								$builder->field($field)->equals($value->value);
								}
							break;

						case SearchParamType::IntEnumArray:
							/** @var IntBackedEnum[] $value */
							if (count($value) > 0)
								{
								$intValues = array_map(static fn($item) => $item->value, $value);
								if ($attribute->invert)
									{
									$builder->field($field)->notIn($intValues);
									}
								else
									{
									$builder->field($field)->in($intValues);
									}
								}
							break;

						case SearchParamType::IntGt:
							$builder->field($field)->exists(true);
							$builder->field($field)->gte(1);
							break;

						case SearchParamType::Range:
							$attribute->direction === SearchParamDirection::From
								? $builder->field($field)->gte($value)
								: $builder->field($field)->lte($value);
							break;

						case SearchParamType::RangeFloat:
							$attribute->direction === SearchParamDirection::From
								? $builder->field($field)->gte((float)$value)
								: $builder->field($field)->lte((float)$value);
							break;

						case SearchParamType::RangeInt:
							$attribute->direction === SearchParamDirection::From
								? $builder->field($field)->gte((int)$value)
								: $builder->field($field)->lte((int)$value);
							break;

						case SearchParamType::String:
							if ($attribute->invert)
								{
								$builder->field($field)->notEqual((string)$value);
								}
							else
								{
								$builder->field($field)->equals((string)$value);
								}
							break;

						case SearchParamType::StringArray:
							if (count($value) > 0)
								{
								if ($attribute->invert)
									{
									$builder->field($field)->notIn($value);
									}
								else
									{
									$builder->field($field)->in($value);
									}
								}
							break;

						case SearchParamType::StringEnum:
							/**
							 * @var StringBackedEnum $value
							 */
							if ($attribute->invert)
								{
								$builder->field($field)->notEqual($value->value);
								}
							else
								{
								$builder->field($field)->equals($value->value);
								}
							break;

						case SearchParamType::StringEnumArray:
							/**
							 * @var StringBackedEnum[] $value
							 */
							if (count($value) > 0)
								{
								$stringValues = array_map(static fn($item) => $item->value, $value);
								if ($attribute->invert)
									{
									$builder->field($field)->notIn($stringValues);
									}
								else
									{
									$builder->field($field)->in($stringValues);
									}
								}
							break;

						case SearchParamType::VirtualBool:
							//Virtual booleans are stored as integers, but are sent as booleans in search
							$builder->field($field)->gte(1);
							break;

						default:
							if ($attribute->callable
								&& is_callable($attribute->callable))
								{
								call_user_func($attribute->callable, $builder, $value, $this);
								}
							break;
						}
					}
				}
			}
		}
	}
