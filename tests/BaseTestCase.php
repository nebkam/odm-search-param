<?php

namespace Nebkam\OdmSearchParam\Tests;

use Doctrine\ODM\MongoDB\Aggregation\Stage\MatchStage;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AttributeDriver;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use MongoDB\Client;
use PHPUnit\Framework\TestCase;


abstract class BaseTestCase extends TestCase
	{
	protected static function assertBuiltMatchStageEquals(MatchStage $matchStage, $query): array
		{
		$expression = $matchStage->getExpression();
		self::assertArrayHasKey('$match', $expression);
		self::assertIsArray($expression['$match']);
		self::assertSame($query, $expression['$match']);

		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $expression['$match'];
		}

	protected static function assertBuiltQueryEquals(Builder $builder, array $query): array
		{
		$debug = $builder->getQuery()->debug();
		self::assertArrayHasKey('query', $debug);
		self::assertIsArray($debug['query']);
		self::assertSame($query, $debug['query']);

		return $debug['query'];
		}

	/**
	 * @param class-string $documentName
	 */
	protected static function createTestAggregationBuilder(string $documentName): \Doctrine\ODM\MongoDB\Aggregation\Builder
		{
		return self::createTestDocumentManager()->createAggregationBuilder($documentName);
		}

	/**
	 * @param class-string $documentName
	 */
	protected static function createTestQueryBuilder(string $documentName): Builder
		{
		return self::createTestDocumentManager()->createQueryBuilder($documentName);
		}

	protected static function createTestDocumentManager(): DocumentManager
		{
		$config = static::getConfiguration();
		$client = new Client('mongodb://localhost:27017', [], [
			'typeMap' => [
				'root'     => 'array',
				'document' => 'array'
			]
		]);

		return DocumentManager::create($client, $config);
		}

	protected static function getConfiguration(): Configuration
		{
		$config = new Configuration();

		$config->setProxyDir(__DIR__ . '/Proxies');
		$config->setProxyNamespace('Proxies');
		$config->setHydratorDir(__DIR__ . '/Hydrators');
		$config->setHydratorNamespace('Hydrators');
		$config->setDefaultDB('test');
		$config->setMetadataDriverImpl(static::createMetadataDriverImpl());

		return $config;
		}

	protected static function createMetadataDriverImpl(): MappingDriver
		{
		return AttributeDriver::create(__DIR__ . '/Documents');
		}
	}
