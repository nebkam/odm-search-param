<?php

namespace Nebkam\OdmSearchParam;

use Doctrine\ODM\MongoDB\Aggregation\Stage\MatchStage;
use Doctrine\ODM\MongoDB\Query\Builder;
use GeoJson\Geometry\Polygon;
use IntBackedEnum;
use InvalidArgumentException;
use MongoDB\BSON\Regex;
use ReflectionClass;
use ReflectionException;
use StringBackedEnum;

class SearchParamParser
	{
	/**
	 * @param $filter
	 * @param Builder|MatchStage $builder
	 * @throws ReflectionException
	 */
	public static function parse($filter, Builder|MatchStage $builder): void
		{
		$reflectionClass = new ReflectionClass($filter);
		foreach ($reflectionClass->getProperties() as $reflectionProperty)
			{
			$reflectionAttributes = $reflectionProperty->getAttributes(SearchParam::class);
			if (!empty($reflectionAttributes))
				{
				foreach ($reflectionAttributes as $reflectionAttribute)
					{
					/** @var SearchParam $attribute */
					$attribute = $reflectionAttribute->newInstance();
					$field     = $attribute->field ?: $reflectionProperty->getName();
					$value     = $reflectionProperty->getValue($filter);
					if ($value !== null)
						{
						match ($attribute->type)
							{
							SearchParamType::Bool => self::setBool($attribute, $builder, $field, $value),
							SearchParamType::Callable => self::setCallable($attribute, $builder, $value, $filter),
							SearchParamType::Exists => self::setExists($attribute, $builder, $field, $value),
							SearchParamType::GeoWithinBox => self::setGeoWithinBox($builder, $field, $value),
							SearchParamType::GeoWithinPolygon => self::setGeoWithinPolygon($builder, $field, $value),
							SearchParamType::Int => self::setInt($attribute, $builder, $field, $value),
							SearchParamType::IntArray => self::setIntArray($attribute, $builder, $field, $value),
							SearchParamType::IntEnum => self::setIntEnum($attribute, $builder, $field, $value),
							SearchParamType::IntEnumArray => self::setIntEnumArray($attribute, $builder, $field, $value),
							SearchParamType::IntGt => self::setIntGt($builder, $field),
							SearchParamType::Range => self::setRange($attribute, $builder, $field, $value),
							SearchParamType::RangeFloat => self::setRangeFloat($attribute, $builder, $field, $value),
							SearchParamType::RangeInt => self::setRangeInt($attribute, $builder, $field, $value),
							SearchParamType::RangeIntEnum => self::setRangeIntEnum($attribute, $builder, $field, $value),
							SearchParamType::Regex => self::setRegex($attribute, $builder, $field, $value),
							SearchParamType::String => self::setString($attribute, $builder, $field, $value),
							SearchParamType::StringArray => self::setStringArray($attribute, $builder, $field, $value),
							SearchParamType::StringEnum => self::setStringEnum($attribute, $builder, $field, $value),
							SearchParamType::StringEnumArray => self::setStringEnumArray($attribute, $builder, $field, $value),
							SearchParamType::VirtualBool => self::setVirtualBoolean($builder, $field),
							};
						}
					}
				}
			}
		}

	private static function setBool(SearchParam $attribute, Builder|MatchStage $builder, string $field, $value): void
		{
		if ($attribute->invert)
			{
			$builder->field($field)->notEqual((bool)$value);
			}
		else
			{
			$builder->field($field)->equals((bool)$value);
			}
		}

	private static function setCallable(SearchParam $attribute, Builder|MatchStage $builder, $value, $filter): void
		{
		if (!$attribute->callable
			|| !is_callable($attribute->callable))
			{
			throw new InvalidArgumentException('Please provide a valid PHP callable in `callable` param');
			}

		call_user_func($attribute->callable, $builder, $value, $filter);
		}

	private static function setExists(SearchParam $attribute, Builder|MatchStage $builder, string $field, $value): void
		{
		$exists = $attribute->invert ? !$value : (bool)$value;
		$builder->field($field)->exists($exists);
		}

	/**
	 * GeoSpatial search within a boundary box, sending `Bottom left` and `Top right` coordinates
	 * @param Builder|MatchStage $builder
	 * @param string $field
	 * @param array{0: float, 1: float, 2: float, 3: float}|mixed $value
	 * @return void
	 * @noinspection PhpMissingParamTypeInspection
	 */
	private static function setGeoWithinBox(Builder|MatchStage $builder, string $field, $value): void
		{
		if (is_countable($value) && count($value) === 4)
			{
			[$bottomLeftX, $bottomLeftY, $topRightX, $topRightY] = $value;
			$builder->field($field)->geoWithinBox($bottomLeftX, $bottomLeftY, $topRightX, $topRightY);
			}
		}

	private static function setGeoWithinPolygon(Builder|MatchStage $builder, string $field, mixed $value): void
		{
		if (is_countable($value) && count($value) === 4)
			{
			[$minLon, $minLat, $maxLon, $maxLat] = $value;
			$builder->field($field)->geoWithin(new Polygon([
				[
					[$minLon, $minLat],  // bottom-left
					[$maxLon, $minLat],  // bottom-right
					[$maxLon, $maxLat],  // top-right
					[$minLon, $maxLat],  // top-left
					[$minLon, $minLat],  // close loop
				]
			]));
			}
		}

	private static function setInt(SearchParam $attribute, Builder|MatchStage $builder, string $field, $value): void
		{
		if ($attribute->invert)
			{
			$builder->field($field)->notEqual((int)$value);
			}
		else
			{
			$builder->field($field)->equals((int)$value);
			}
		}

	private static function setIntArray(SearchParam $attribute, Builder|MatchStage $builder, string $field, $values): void
		{
		if (count($values) > 0)
			{
			$intValues = array_map(static fn($item) => (int)$item, $values);
			if ($attribute->invert)
				{
				$builder->field($field)->notIn($intValues);
				}
			else
				{
				$builder->field($field)->in($intValues);
				}
			}
		}

	/**
	 * @param SearchParam $attribute
	 * @param Builder|MatchStage $builder
	 * @param string $field
	 * @var IntBackedEnum $value
	 * @noinspection PhpMissingParamTypeInspection
	 */
	private static function setIntEnum(SearchParam $attribute, Builder|MatchStage $builder, string $field, $value): void
		{
		if ($attribute->invert)
			{
			$builder->field($field)->notEqual($value->value);
			}
		else
			{
			$builder->field($field)->equals($value->value);
			}
		}

	/**
	 * @param SearchParam $attribute
	 * @param Builder|MatchStage $builder
	 * @param string $field
	 * @param IntBackedEnum[] $values
	 * @return void
	 * @noinspection PhpMissingParamTypeInspection
	 */
	private static function setIntEnumArray(SearchParam $attribute, Builder|MatchStage $builder, string $field, $values): void
		{
		if (count($values) > 0)
			{
			$intValues = array_map(static fn($item) => $item->value, $values);
			if ($attribute->invert)
				{
				$builder->field($field)->notIn($intValues);
				}
			else
				{
				$builder->field($field)->in($intValues);
				}
			}
		}

	private static function setIntGt(Builder|MatchStage $builder, string $field): void
		{
		$builder
			->field($field)->exists(true)
			->field($field)->gte(1);
		}

	private static function setRange(SearchParam $attribute, Builder|MatchStage $builder, string $field, $value): void
		{
		$attribute->direction === SearchParamDirection::From
			? $builder->field($field)->gte($value)
			: $builder->field($field)->lte($value);
		}

	private static function setRangeFloat(SearchParam $attribute, Builder|MatchStage $builder, string $field, $value): void
		{
		$attribute->direction === SearchParamDirection::From
			? $builder->field($field)->gte((float)$value)
			: $builder->field($field)->lte((float)$value);
		}

	private static function setRangeInt(SearchParam $attribute, Builder|MatchStage $builder, string $field, $value): void
		{
		$attribute->direction === SearchParamDirection::From
			? $builder->field($field)->gte((int)$value)
			: $builder->field($field)->lte((int)$value);
		}

	/**
	 * IntBackedEnum $value
	 */
	private static function setRangeIntEnum(SearchParam $attribute, Builder|MatchStage $builder, string $field, $value): void
		{
		$attribute->direction === SearchParamDirection::From
			? $builder->field($field)->gte((int)$value->value)
			: $builder->field($field)->lte((int)$value->value);
		}

	private static function setRegex(SearchParam $attribute, Builder|MatchStage $builder, string $field, mixed $value): void
		{
		if ($value)
			{
			$builder->field($field)->equals(new Regex($value, $attribute->flags ?? ''));
			}
		}

	private static function setString(SearchParam $attribute, Builder|MatchStage $builder, string $field, $value): void
		{
		if ($attribute->invert)
			{
			$builder->field($field)->notEqual((string)$value);
			}
		else
			{
			$builder->field($field)->equals((string)$value);
			}
		}

	private static function setStringArray(SearchParam $attribute, Builder|MatchStage $builder, string $field, $values): void
		{
		if (count($values) > 0)
			{
			if ($attribute->invert)
				{
				$builder->field($field)->notIn($values);
				}
			else
				{
				$builder->field($field)->in($values);
				}
			}
		}

	/**
	 * @param SearchParam $attribute
	 * @param Builder|MatchStage $builder
	 * @param string $field
	 * @param StringBackedEnum $value
	 * @noinspection PhpMissingParamTypeInspection
	 */
	private static function setStringEnum(SearchParam $attribute, Builder|MatchStage $builder, string $field, $value): void
		{
		if ($attribute->invert)
			{
			$builder->field($field)->notEqual($value->value);
			}
		else
			{
			$builder->field($field)->equals($value->value);
			}
		}

	/**
	 * @param SearchParam $attribute
	 * @param Builder|MatchStage $builder
	 * @param string $field
	 * @var StringBackedEnum[] $values
	 * @noinspection PhpMissingParamTypeInspection
	 */
	private static function setStringEnumArray(SearchParam $attribute, Builder|MatchStage $builder, string $field, $values): void
		{
		if (count($values) > 0)
			{
			$stringValues = array_map(static fn($enum) => $enum->value, $values);
			if ($attribute->invert)
				{
				$builder->field($field)->notIn($stringValues);
				}
			else
				{
				$builder->field($field)->in($stringValues);
				}
			}
		}

	/**
	 * Virtual booleans are stored as integers, but are sent as booleans in search
	 */
	private static function setVirtualBoolean(Builder|MatchStage $builder, string $field): void
		{
		$builder->field($field)->gte(1);
		}
	}
