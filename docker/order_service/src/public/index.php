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
			'result' => 'I am order service'
		];
	}
);

$app->addRoute(
	HttpMethod::POST,
	'/order/create',
	static function (Context $context): array
	{
		$data = $context->getRequest()->getParsedBody();

		// обеспечение идемпотентности при создании заказа
		$requestId = '';
		if ($context->getRequest()->hasHeader('X-Request-Id'))
		{
			$requestId = $context->getRequest()->getHeaderLine('X-Request-Id');
			$order = \OrderService\Order::getByIdempotenceKey($requestId);
			if ($order)
			{
				return $order->toArray();
			}
		}

		return \OrderService\Order::create(
			$data['clientId'] ?? '',
			$data['sum'] ?? '',
			$requestId
		)->toArray();
	}
);

$app->addRoute(
	HttpMethod::POST,
	'/order/confirm',
	static function (Context $context): array
	{
		$data = $context->getRequest()->getParsedBody();

		$orderId = $data['orderId'] ?? null;

		if (!$orderId)
		{
			return [
				'error' => 'Order id is empty'
			];
		}
		$order = \OrderService\Order::getById((int)$orderId);
		if (!$order)
		{
			return [
				'error' => 'Order not found'
			];
		}
		$order
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
	'/order/reject',
	static function (Context $context): array
	{
		$data = $context->getRequest()->getParsedBody();

		$orderId = $data['orderId'] ?? null;

		if (!$orderId)
		{
			return [
				'error' => 'Order id is empty'
			];
		}
		$order = \OrderService\Order::getById((int)$orderId);
		if (!$order)
		{
			return [
				'error' => 'Order not found'
			];
		}
		$order->delete(); // удалить

		return [
			'status' => 'OK'
		];
	}
);

$app->run();
