<?php

use BaseMicroservice\Application\Context;
use \BaseMicroservice\Application\HttpMethod;

require __DIR__ . '/../vendor/autoload.php';

$config = \NotificationService\Config::createFromEnv();
$app = new BaseMicroservice\Application($config);
\BaseMicroservice\Database::getInstance()->init($config);

$app->addRoute(
	HttpMethod::GET,
	'/',
	static function (Context $context): array
	{
		return [
			'result' => 'I am notification service'
		];
	}
);

$app->addRoute(
	HttpMethod::POST,
	'/notify/newClientCreated',
	static function (Context $context): array
	{
		$data = (array)$context->getRequest()->getParsedBody();

		// обеспечение идемпотентности при создании уведомления
		$requestId = '';
		if ($context->getRequest()->hasHeader('X-Request-Id'))
		{
			$requestId = $context->getRequest()->getHeaderLine('X-Request-Id');
			$existed = \NotificationService\Notification::getByIdempotenceKey($requestId);
			if ($existed)
			{
				return $existed->toArray();
			}
		}

		$notificataion = \NotificationService\Notification::create(
			'newClientCreated',
			$data,
			$requestId
		);

		if ($notificataion)
		{
			return $notificataion->toArray();
		}

		return [
			'error' => 'Notification was not created'
		];
	}
);

$app->addRoute(
	HttpMethod::POST,
	'/notify/newOrderCreated',
	static function (Context $context): array
	{
		$data = (array)$context->getRequest()->getParsedBody();

		// обеспечение идемпотентности при создании уведомления
		$requestId = '';
		if ($context->getRequest()->hasHeader('X-Request-Id'))
		{
			$requestId = $context->getRequest()->getHeaderLine('X-Request-Id');
			$existed = \NotificationService\Notification::getByIdempotenceKey($requestId);
			if ($existed)
			{
				return $existed->toArray();
			}
		}

		if (isset($data['clientId']) && (int)$data['clientId'] > 0)
		{
			$config = $context->getApplication()->getConfig();
			$clientService = new \NotificationService\ClientService($config);
			$clientServiceResult = $clientService->getClientById((int)$data['clientId']);
			if ($clientServiceResult->isSuccess())
			{
				$data['client'] = $clientServiceResult->getData();
			}
			else
			{
				return [
					'error' => $clientServiceResult->getError(),
				];
			}
		}

		$notificataion = \NotificationService\Notification::create(
			'newOrderCreated',
			$data,
			$requestId
		);

		if ($notificataion)
		{
			return $notificataion->toArray();
		}

		return [
			'error' => 'Notification was not created',
		];
	}
);

$app->addRoute(
	HttpMethod::GET,
	'/notification/last',
	static function (Context $context): array
	{
		$notification = \NotificationService\Notification::getLast();
		if ($notification)
		{
			return $notification->toArray();
		}

		return [
			'error' => 'Nothing found',
		];
	}
);

$app->run();
