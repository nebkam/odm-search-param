<?php

namespace Nebkam\OdmSearchParam\Tests\Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document]
class SearchableDocument
	{
	#[ODM\Id]
	public string $id;
	}
