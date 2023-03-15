<?php

namespace Nebkam\OdmSearchParam\Tests;

use Doctrine\ODM\MongoDB\Aggregation\Stage\MatchStage;
use Doctrine\ODM\MongoDB\Query\Builder;

class SearchFilterDependency
	{
	/**
	 * @param Builder|MatchStage $builder
	 * @param $value
	 * @param $filter
	 */
	public static function setFoo($builder, $value, $filter): void
		{
		$builder->field('foo')->equals($value);
		}
	}
