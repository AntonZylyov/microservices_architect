<?php

namespace NotificationService;

use BaseMicroservice\Application\HttpMethod;
use BaseMicroservice\Service;
use BaseMicroservice\Result;

class ClientService extends Service
{
	protected function getHost(): string
	{
		return $this->config->getClientServiceHost();
	}

	public function getClientById(int $clientId): Result
	{
		return $this->request(
			'/client/' . $clientId,
			[],
			HttpMethod::GET
		);
	}
}
