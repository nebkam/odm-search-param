<?php

namespace Nebkam\OdmSearchParam\Tests;

use Doctrine\ODM\MongoDB\Query\Builder;

class SearchFilterDependency
	{
	public static function setFoo(Builder $builder, $value, $filter): void
		{
		$builder->field('foo')->equals($value);
		}
	}
