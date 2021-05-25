<?php

namespace OrderServiceApp;


class NotificationService
{
	protected $host = '';
	public function __construct()
	{
		$config = Config::getInstance();
		$this->host = $config->getNotificationServiceHost();
	}

	public function notifyNewOrderSuccess(int $orderId, int $userId, float $sum): void
	{
		$client = new \GuzzleHttp\Client();
		$client->request(
			'POST',
			$this->host.'/notify/newOrderSuccess',
			[
				'body' => json_encode(
				[
					'orderId' => $orderId,
					'userId' => $userId,
					'sum' => $sum
				])
			]
		);
	}

	public function notifyNewOrderFailure(int $userId, float $sum): void
	{
		$client = new \GuzzleHttp\Client();
		$client->request(
			'POST',
			$this->host.'/notify/newOrderFailure',
			[
				'body' => json_encode(
					[
						'userId' => $userId,
						'sum' => $sum
					])
			]
		);
	}
}