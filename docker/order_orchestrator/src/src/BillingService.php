<?php

namespace OrderOrchestrator;

use BaseMicroservice\Application\HttpMethod;
use BaseMicroservice\Service;
use BaseMicroservice\Result;

class BillingService extends Service
{
	protected function getHost(): string
	{
		return $this->config->getBillingServiceHost();
	}

	public function withdraw(int $clientId, float $sum): Result
	{
		return $this->request(
			'/billing/withdraw',
			[
				'clientId' => $clientId,
				'sum' => $sum,
			],
			HttpMethod::POST
		);
	}
}
