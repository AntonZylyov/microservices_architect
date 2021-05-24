<?php

use BaseMicroservice\Application\Context;
use \BaseMicroservice\Application\HttpMethod;

require __DIR__ . '/../vendor/autoload.php';

$app = new BaseMicroservice\Application(\OrderOrchestrator\Config::createFromEnv());

$app->addRoute(
	HttpMethod::GET,
	'/',
	static function (Context $context): array
	{
		return [
			'result' => 'I am order orchestrator'
		];
	}
);

$app->addRoute(
	HttpMethod::POST,
	'/create_order',
	static function (Context $context): array
	{
		// BFF в заголовках передает текущего авторизованного клиента
		// а если не передал, то что-то пошло не так
		if (!$context->getRequest()->hasHeader('X-ClientId'))
		{
			return [
				'error' => 'Unauthorized',
			];
		}

		$data = $context->getRequest()->getParsedBody();

		$clientId = (int)$context->getRequest()->getHeaderLine('X-ClientId');
		$sum = (float)($data['sum'] ?? 0);
		if ($sum <= 0)
		{
			return [
				'error' => 'Wrong sum',
			];
		}
		$config = $context->getApplication()->getConfig();

		// начинается сага:
		// создается заказ
		$orderService = new \OrderOrchestrator\OrderService($config);
		$createOrderResult = $orderService->createOrder($clientId, $sum);
		if (!$createOrderResult->isSuccess())
		{
			return [
				'error' => $createOrderResult->getError(),
			];
		}
		$orderId = (int)$createOrderResult->getData()['id'];

		// списываются деньги
		$billingService = new \OrderOrchestrator\BillingService($config);
		$withdrawResult = $billingService->withdraw($clientId, $sum);

		// в зависимости от успешности "поворотной" транзакции списания денег,
		// сага либо завершается, либо компенсируется (откатывается)
		if ($withdrawResult->isSuccess())
		{
			$orderService->confirmOrder($orderId);
			$notificationService = new \OrderOrchestrator\NotificationService($config);
			$notificationService->notifyNewOrderCreated(
				$clientId,
				$orderId,
				$sum
			);
		}
		else
		{
			$orderService->rejectOrder($orderId);
			return [
				'error' => $withdrawResult->getError(),
			];
		}

		return [
			'success' => 'ok'
		];
	}
);

$app->run();
