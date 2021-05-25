<?php

namespace OrderOrchestrator;

use BaseMicroservice\Application\HttpMethod;
use BaseMicroservice\Service;
use BaseMicroservice\Result;

class NotificationService extends Service
{
	protected function getHost(): string
	{
		return $this->config->getNotificationServiceHost();
	}

	public function notifyNewOrderCreated(
		int $clientId,
		int $orderId,
		float $sum
	): Result
	{
		return $this->request(
			'/notify/newOrderCreated',
			[
				'clientId' => $clientId,
				'orderId' => $orderId,
				'sum' => $sum,
			],
			HttpMethod::POST
		);
	}

}
