<?php

namespace Nebkam\OdmSearchParam;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ODM\MongoDB\Aggregation\Stage\MatchStage;
use Doctrine\ODM\MongoDB\Query\Builder;
use ReflectionClass;

trait SearchParamParseable
	{
	/**
	 * @param Builder|MatchStage $builder
	 * @param Reader $annotationReader
	 * @return Builder|MatchStage
	 */
	public function parseSearchParam($builder, Reader $annotationReader)
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
									if ($annotation->invert)
										{
										$builder->field($field)->notIn($int_values);
										}
									else
										{
										$builder->field($field)->in($int_values);
										}
									}
								break;
								
							case 'int_gt':
									$builder->field($field)->exists(true);
									$builder->field($field)->gte(1);
									break;

							case 'string_array':
								if (count($value) > 0)
									{
									if ($annotation->invert)
										{
										$builder->field($field)->notIn($value);
										}
									else
										{
										$builder->field($field)->in($value);
										}
									}
								break;

							case 'range':
								$annotation->direction === 'from'
									? $builder->field($field)->gte($value)
									: $builder->field($field)->lte($value);
								break;

							case 'range_int':
								$annotation->direction === 'from'
									? $builder->field($field)->gte( (int) $value)
									: $builder->field($field)->lte( (int) $value);
								break;

							case 'range_float':
								$annotation->direction === 'from'
									? $builder->field($field)->gte( (float) $value)
									: $builder->field($field)->lte( (float) $value);
								break;

							case 'exists':
								$builder->field($field)->exists((bool) $value);
								break;

							case 'int':
								if ($annotation->invert)
									{
									$builder->field($field)->notEqual((int) $value);
									}
								else
									{
									$builder->field($field)->equals((int) $value);
									}
								break;

							case 'string':
							case 'bool':
								if ($annotation->invert)
									{
									$builder->field($field)->notEqual($value);
									}
								else
									{
									$builder->field($field)->equals($value);
									}
								break;

							case 'virtual_bool':
								//Virtual booleans are stored as integers, but are sent as booleans in search
								$builder->field($field)->gte(1);
								break;

							default:
								if ($annotation->callable
									&& is_callable($annotation->callable))
									{
									call_user_func($annotation->callable, $builder, $value, $this);
									}
								break;
							}
						}
					}
				}
			}

		return $builder;
		}
	}
