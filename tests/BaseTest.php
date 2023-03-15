<?php

namespace Nebkam\OdmSearchParam\Tests;

use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use MongoDB\Client;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
	{
	protected DocumentManager $dm;

	public function setUp() : void
		{
		$this->dm = $this->createTestDocumentManager();
		}

	protected function createTestDocumentManager(): DocumentManager
		{
		$config = $this->getConfiguration();
		$client = new Client('mongodb://localhost:27017', [], ['typeMap' => ['root' => 'array', 'document' => 'array']]);

		return DocumentManager::create($client, $config);
		}

	protected function getConfiguration(): Configuration
		{
		$config = new Configuration();

		$config->setProxyDir(__DIR__ . '/Proxies');
		$config->setProxyNamespace('Proxies');
		$config->setHydratorDir(__DIR__ . '/Hydrators');
		$config->setHydratorNamespace('Hydrators');
//		$config->setPersistentCollectionDir(__DIR__ . '/../../../../PersistentCollections');
//		$config->setPersistentCollectionNamespace('PersistentCollections');
		$config->setDefaultDB('test');
		$config->setMetadataDriverImpl($this->createMetadataDriverImpl());

		return $config;
		}

	protected function createMetadataDriverImpl(): AnnotationDriver
		{
		return AnnotationDriver::create(__DIR__ . '/Documents');
		}
	}
