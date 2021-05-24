<?php

namespace ClientOrchestrator;

use BaseMicroservice\Application\HttpMethod;
use BaseMicroservice\Service;
use BaseMicroservice\Result;

class NotificationService extends Service
{
	protected function getHost(): string
	{
		return $this->config->getNotificationServiceHost();
	}

	public function notifyNewClientCreated(
		string $lastName,
		string $firstName,
		string $email,
		string $login
	): Result
	{
		return $this->request(
			'/notify/newClientCreated',
			[
				'lastName' => $lastName,
				'firstName' => $firstName,
				'email' => $email,
				'login' => $login,
			],
			HttpMethod::POST
		);
	}
}
