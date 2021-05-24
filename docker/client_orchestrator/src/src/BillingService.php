<?php

namespace ClientOrchestrator;

use BaseMicroservice\Application\HttpMethod;
use BaseMicroservice\Service;
use BaseMicroservice\Result;

class BillingService extends Service
{
	protected function getHost(): string
	{
		return $this->config->getBillingServiceHost();
	}

	public function createAccount(int $clientId): Result
	{
		return $this->request(
			'/billing/createAccount',
			[
				'clientId' => $clientId,
			],
			HttpMethod::POST
		);
	}
}