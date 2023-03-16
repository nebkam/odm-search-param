<?php

namespace Nebkam\OdmSearchParam\Tests;

use Doctrine\ODM\MongoDB\Aggregation\Stage\MatchStage;
use Doctrine\ODM\MongoDB\Query\Builder;

class SearchFilterDependency
	{
	public static function setFoo(Builder|MatchStage $builder, $value, $filter): void
		{
		$builder->field('foo')->equals($value);
		}
	}
