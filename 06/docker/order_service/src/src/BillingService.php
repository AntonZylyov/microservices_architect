<?php

namespace OrderServiceApp;


class BillingService
{
	protected $host = '';
	public function __construct()
	{
		$config = \OrderServiceApp\Config::getInstance();
		$this->host = $config->getBillingServiceHost();
	}
	public function withdraw(int $userId, float $sum = 0): array
	{
		$client = new \GuzzleHttp\Client();
		$response = $client->request(
			'POST',
			$this->host.'/billing/withdraw',
			[
				'body' => json_encode(
				[
					'userId' => $userId,
					'sum' => $sum
				])
			]
		);
		$data = json_decode($response->getBody()->getContents(), true);
		if (!is_array($data))
		{
			$data = [
				'error' => 'Не удалось выполнить списание'
			];
		}
		return $data;
	}
}