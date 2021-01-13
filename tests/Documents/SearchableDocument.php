<?php

namespace Nebkam\OdmSearchParam\Tests\Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;

/**
 * @Document()
 */
class SearchableDocument
	{
	/**
	 * @Id
	 * @var string
	 */
	private string $id;
	}
