<?php

namespace Nebkam\OdmSearchParam;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ODM\MongoDB\Query\Builder;
use ReflectionClass;
use ReflectionException;

trait SearchParamParseable
	{
	/**
	 * @param Builder $queryBuilder
	 * @param Reader $annotationReader
	 * @return Builder
	 * @throws ReflectionException
	 */
	public function parseSearchParam(Builder $queryBuilder, Reader $annotationReader) : Builder
		{
		$reflectionClass = new ReflectionClass($this);

		foreach ($this as $property => $value)
			{
			if ($value !== null && $reflectionClass->hasProperty($property))
				{
				$reflectionProperty = $reflectionClass->getProperty($property);
				$annotations = $annotationReader->getPropertyAnnotations($reflectionProperty);
				foreach ($annotations as $annotation)
					{
					if ($annotation instanceof SearchParam)
						{
						$field = $annotation->field ?: $property;

						switch ($annotation->type)
							{
							case 'int_array':
								if (count($value) > 0)
									{
									$int_values = array_map(static function($item){ return (int) $item; }, $value);
									$queryBuilder->field($field)->in($int_values);
									}
								break;
								
							case 'int_gt':
									$queryBuilder->field($field)->exists(true);
									$queryBuilder->field($field)->gte(1);
									break;

							case 'string_array':
								if (count($value) > 0)
									{
									$queryBuilder->field($field)->in($value);
									}
								break;

							case 'range_int':
								$annotation->direction === 'from'
									? $queryBuilder->field($field)->gte( (int) $value)
									: $queryBuilder->field($field)->lte( (int) $value);
								break;

							case 'range_float':
								$annotation->direction === 'from'
									? $queryBuilder->field($field)->gte( (float) $value)
									: $queryBuilder->field($field)->lte( (float) $value);
								break;

							case 'exists':
								$queryBuilder->field($field)->exists((bool) $value);
								break;

							case 'int':
								$queryBuilder->field($field)->equals((int) $value);
								break;

							case 'string':
							case 'bool':
								$queryBuilder->field($field)->equals($value);
								break;

							case 'virtual_bool':
								//Virtual booleans are stored as integers, but are sent as booleans in search
								$queryBuilder->field($field)->gte(1);
								break;

							default:
								//TODO use a real callable
								if ($annotation->callback)
									{
									$queryBuilder = $this->{$annotation->callback}($queryBuilder,$value);
									}
								break;
							}
						}
					}
				}
			}

		return $queryBuilder;
		}
	}
