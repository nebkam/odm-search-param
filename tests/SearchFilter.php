<?php

namespace Nebkam\OdmSearchParam\Tests;

use Nebkam\OdmSearchParam\SearchParam;
use Nebkam\OdmSearchParam\SearchParamType;

class SearchFilter
	{
	#[SearchParam(type: SearchParamType::String)]
	public ?string $stringProperty = null;
	}
