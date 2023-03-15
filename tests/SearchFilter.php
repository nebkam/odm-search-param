<?php

namespace Nebkam\OdmSearchParam\Tests;

use Nebkam\OdmSearchParam\SearchParam;
use Nebkam\OdmSearchParam\Type;

class SearchFilter
	{
	#[SearchParam(type: Type::String)]
	public ?string $stringProperty = null;
	}
