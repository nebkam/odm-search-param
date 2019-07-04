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
	public $type;
	/**
	 * @Enum({"from", "to"})
	 * Used with type `range_int` and `range_float`
	 */
	public $direction;
	/**
	 * @var string Explicitly name the field that the property value applies to. Defaults to property name.
	 */
	public $field;
	/**
	 * Method name on the search filter
	 * that's being called with the query builder and filter property value as an arguments
	 * and returns the decorated query builder
	 * @var string
	 */
	public $callback;
	}
