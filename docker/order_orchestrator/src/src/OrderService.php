<?php

namespace OrderOrchestrator;


use BaseMicroservice\Application\HttpMethod;
use BaseMicroservice\Service;
use BaseMicroservice\Result;

class OrderService extends Service
{
	protected function getHost(): string
	{
		return $this->config->getOrderServiceHost();
	}

	public function createOrder(int $clientId, float $sum): Result
	{
		return $this->request(
			'/order/create',
			[
				'clientId' => $clientId,
				'sum' => $sum,
			],
			HttpMethod::POST
		);
	}

	public function confirmOrder(int $orderId): Result
	{
		return $this->request(
			'/order/confirm',
			[
				'orderId' => $orderId,
			],
			HttpMethod::POST
		);
	}

	public function rejectOrder(int $orderId): Result
	{
		return $this->request(
			'/order/reject',
			[
				'orderId' => $orderId,
			],
			HttpMethod::POST
		);
	}
}
