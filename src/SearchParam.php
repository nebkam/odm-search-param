<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Nebkam\OdmSearchParam;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class SearchParam
	{
	public function __construct(
		public ?SearchParamType      $type = null,
		public ?SearchParamDirection $direction = null,
		public ?string               $field = null,
		public ?array                $callable = null,
		public ?bool                 $invert = null
	)
		{
		}
	}
