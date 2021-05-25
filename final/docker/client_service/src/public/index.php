<?php

use BaseMicroservice\Application\Context;
use \BaseMicroservice\Application\HttpMethod;

require __DIR__ . '/../vendor/autoload.php';

$config = \BaseMicroservice\Config::createFromEnv();
$app = new BaseMicroservice\Application($config);
\BaseMicroservice\Database::getInstance()->init($config);

$app->addRoute(
	HttpMethod::GET,
	'/',
	static function (Context $context): array
	{
		return [
			'result' => 'I am client service'
		];
	}
);

$app->addRoute(
	HttpMethod::POST,
	'/client/add',
	static function (Context $context): array
	{
		$data = $context->getRequest()->getParsedBody();

		// обеспечение идемпотентности при создании клиента
		$requestId = '';
		if ($context->getRequest()->hasHeader('X-Request-Id'))
		{
			$requestId = $context->getRequest()->getHeaderLine('X-Request-Id');
			$client = \ClientService\Client::getByIdempotenceKey($requestId);
			if ($client)
			{
				return $client->toArray();
			}
		}

		return \ClientService\Client::create(
			$data['firstName'] ?? '',
			$data['lastName'] ?? '',
			$data['email'] ?? '',
			$requestId
		)->toArray();
	}
);

$app->addRoute(
	HttpMethod::GET,
	'/client/{clientId}',
	static function (Context $context): array
	{
		$clientId = $context->getRouteArgs()['clientId'] ?? null;

		if (!$clientId)
		{
			return [
				'error' => 'Client id was not found'
			];
		}
		$client = \ClientService\Client::getById((int)$clientId);
		if ($client)
		{
			return $client->toArray();
		}

		return [
			'error' => 'Client not found'
		];
	}
);

$app->addRoute(
	HttpMethod::POST,
	'/client/approve',
	static function (Context $context): array
	{
		$data = $context->getRequest()->getParsedBody();

		$clientId = $data['clientId'] ?? null;

		if (!$clientId)
		{
			return [
				'error' => 'Client id? Where is client id?'
			];
		}
		$client = \ClientService\Client::getById((int)$clientId);
		if (!$client)
		{
			return [
				'error' => 'Client ne client'
			];
		}
		$client
			->setIsPending(false)
			->save()
		;

		return [
			'status' => 'OK'
		];
	}
);


$app->addRoute(
	HttpMethod::POST,
	'/client/reject',
	static function (Context $context): array
	{
		$data = $context->getRequest()->getParsedBody();

		$clientId = $data['clientId'] ?? null;

		if (!$clientId)
		{
			return [
				'error' => 'Client id? Where is client id?'
			];
		}
		$client = \ClientService\Client::getById((int)$clientId);
		if (!$client)
		{
			return [
				'error' => 'Client ne client'
			];
		}
		$client->delete(); // удалить

		return [
			'status' => 'OK'
		];
	}
);

$app->run();
