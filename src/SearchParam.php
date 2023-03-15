<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Nebkam\OdmSearchParam;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class SearchParam
	{
	public function __construct(
		public ?Type $type = null
	)
		{
		}
	}
