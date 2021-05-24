<?php

use BaseMicroservice\Application\Context;
use \BaseMicroservice\Application\HttpMethod;

require __DIR__ . '/../vendor/autoload.php';

$config = \BaseMicroservice\Config::createFromEnv();
$app = new BaseMicroservice\Application($config);
\BaseMicroservice\Database::getInstance()->init($config);

$app->addRoute(
	HttpMethod::GET,
	'/',
	static function (Context $context): array
	{
		return [
			'result' => 'I am billing service'
		];
	}
);

$app->addRoute(
	HttpMethod::POST,
	'/billing/createAccount',
	static function (Context $context): array
	{
		$data = $context->getRequest()->getParsedBody();
		$clientId = isset($data['clientId']) ? (int)$data['clientId'] : 0;
		if ($clientId <=0)
		{
			return [
				'error' => 'Client id is empty'
			];
		}

		// обеспечение идемпотентности при создании учетки
		$requestId = '';
		if ($context->getRequest()->hasHeader('X-Request-Id'))
		{
			$requestId = $context->getRequest()->getHeaderLine('X-Request-Id');
			$account = \BillingService\Account::getByIdempotenceKey($requestId);
			if ($account)
			{
				return $account->toArray();
			}
		}

		$existedAccount = \BillingService\Account::getByClientId($clientId);
		if ($existedAccount)
		{
			return [
				'error' => 'Account already exists'
			];
		}

		$account = \BillingService\Account::create(
			$clientId,
			$requestId
		);
		if ($account)
		{
			return $account->toArray();
		}

		return [
			'error' => 'Account was not created'
		];
	}
);

$app->addRoute(
	HttpMethod::GET,
	'/billing/{clientId}',
	static function (Context $context): array
	{
		$clientId = $context->getRouteArgs()['clientId'] ??  0;
		if ($clientId <=0)
		{
			return [
				'error' => 'Client id is empty'
			];
		}

		$account = \BillingService\Account::getByClientId($clientId);
		if ($account)
		{
			return $account->toArray();
		}

		return [
			'error' => 'Client not found'
		];
	}
);

$app->addRoute(
	HttpMethod::POST,
	'/billing/withdraw',
	static function (Context $context): array
	{
		$data = $context->getRequest()->getParsedBody();
		$clientId = isset($data['clientId']) ? (int)$data['clientId'] : 0;
		if ($clientId <= 0)
		{
			return [
				'error' => 'Client id is empty'
			];
		}
		$sum = isset($data['sum']) ? (float)$data['sum'] : 0;
		if ($sum <= 0)
		{
			return [
				'error' => 'Wrong sum'
			];
		}

		$account = \BillingService\Account::getByClientId($clientId);
		if (!$account)
		{
			return [
				'error' => 'Account not found'
			];
		}

		if ($account->getBalance() < $sum)
		{
			return [
				'error' => 'Денег нет но вы держитесь'
			];
		}
		// тут явно не хватает проверки идемпотентности запроса на изменение баланса, но
		// реализовывать его я не хочу
		$account->addToBalance(-$sum);

		return $account->toArray();
	}
);

$app->addRoute(
	HttpMethod::POST,
	'/billing/deposit',
	static function (Context $context): array
	{
		$data = $context->getRequest()->getParsedBody();
		$clientId = isset($data['clientId']) ? (int)$data['clientId'] : 0;
		if ($clientId <= 0)
		{
			return [
				'error' => 'Client id is empty'
			];
		}
		$sum = isset($data['sum']) ? (float)$data['sum'] : 0;
		if ($sum <= 0)
		{
			return [
				'error' => 'Wrong sum'
			];
		}

		$account = \BillingService\Account::getByClientId($clientId);
		if (!$account)
		{
			return [
				'error' => 'Account not found'
			];
		}
		// тут явно не хватает проверки идемпотентности запроса на изменение баланса, но
		// реализовывать его я не хочу
		$account->addToBalance($sum);

		return $account->toArray();
	}
);

$app->run();
