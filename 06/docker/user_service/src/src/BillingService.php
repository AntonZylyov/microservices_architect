<?php

namespace UserServiceApp;


class BillingService
{
	protected $host = '';
	public function __construct()
	{
		$config = \UserServiceApp\Config::getInstance();
		$this->host = $config->getBillingServiceHost();
	}
	public function createAccount(int $userId, float $initialSum = 0): void
	{
		$client = new \GuzzleHttp\Client();
		$client->request(
			'POST',
			$this->host.'/billing/createAccount',
			[
				'body' => json_encode(
				[
					'userId' => $userId,
					'initialSum' => $initialSum
				])
			]
		);
	}
}