<?php

namespace Nebkam\OdmSearchParam;

use Doctrine\Common\Annotations\Annotation\Enum;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class SearchParam
	{
	/**
	 * @Enum({"string", "string_array", "int", "int_array", "int_gt", "bool", "virtual_bool", "range_int", "range_float", "exists"})
	 */
	public ?string $type = null;
	/**
	 * @Enum({"from", "to"})
	 * Used with type `range_int` and `range_float`
	 */
	public ?string $direction = null;
	/**
	 * Explicitly name the field that the property value applies to. Defaults to property name.
	 */
	public ?string $field = null;
	/**
	 * Method to call with the query builder, filter property value and the whole filter instance as arguments
	 */
	public ?array $callable = null;
	/**
	 * Invert the comparison
	 * Currently only `int_array` and `string_array` types supported
	 */
	public ?bool $invert = null;
	}
