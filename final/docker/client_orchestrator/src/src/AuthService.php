<?php

namespace ClientOrchestrator;


use BaseMicroservice\Application\HttpMethod;
use BaseMicroservice\Service;
use BaseMicroservice\Result;

class AuthService extends Service
{
	protected function getHost(): string
	{
		return $this->config->getAuthServiceHost();
	}

	public function register(string $login, string $password, int $clientId): Result
	{
		return $this->request(
			'/user/register',
			[
				'login' => $login,
				'password' => $password,
				'clientId' => $clientId,
			],
			HttpMethod::POST
		);
	}
}
