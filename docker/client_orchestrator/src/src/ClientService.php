<?php

namespace ClientOrchestrator;

use BaseMicroservice\Application\HttpMethod;
use BaseMicroservice\Service;
use BaseMicroservice\Result;

class ClientService extends Service
{
	protected function getHost(): string
	{
		return $this->config->getClientServiceHost();
	}

	public function createClient(string $lastName, string $firstName, string $email): Result
	{
		return $this->request(
			'/client/add',
			[
				'lastName' => $lastName,
				'firstName' => $firstName,
				'email' => $email,
			],
			HttpMethod::POST
		);
	}

	public function approveClient(int $clientId): Result
	{
		return $this->request(
			'/client/approve',
			[
				'clientId' => $clientId,
			],
			HttpMethod::POST
		);
	}

	public function rejectClient(int $clientId): Result
	{
		return $this->request(
			'/client/reject',
			[
				'clientId' => $clientId,
			],
			HttpMethod::POST
		);
	}
}
