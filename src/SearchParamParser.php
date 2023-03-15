<?php

namespace Nebkam\OdmSearchParam;

use Doctrine\ODM\MongoDB\Aggregation\Stage\MatchStage;
use Doctrine\ODM\MongoDB\Query\Builder;
use ReflectionClass;
use ReflectionException;

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
			if ($reflectionProperty->getAttributes(SearchParam::class))
				{
				$property = $reflectionProperty->getName();
				$value    = $reflectionProperty->getValue($filter);
				if ($value !== null)
					{
					$builder->field($property)->equals($value);
					}
				}
			}
		}
	}
