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
			'result' => 'I am auth service'
		];
	}
);

$app->addRoute(
	HttpMethod::ANY,
	'/authorize',
	static function (Context $context): array
	{
		$sessionId = (string)($context->getRequest()->getCookieParams()['session'] ?? '');

		if ($sessionId !== '')
		{
			$session = new \AuthService\Session();
			$session->setSessionId($sessionId);
			$user = $session->findUser();
			if($user)
			{
				$context->addResponseHeader('X-UserId', $user->getId());
				$context->addResponseHeader('X-ClientId', (string)$user->getClientId());

				return [
					'status' => 'OK'
				];
			}
		}

		$context->getResponse()
			->withStatus(401, 'Unauthorized');

		return [
			'error' => 'Unauthorized'
		];
	}
);

$app->addRoute(
	HttpMethod::GET,
	'/logout',
	static function (Context $context): array
	{
		$sessionId = (string)($context->getRequest()->getCookieParams()['session'] ?? '');

		if ($sessionId !== '')
		{
			$session = new \AuthService\Session();
			$session
				->setSessionId($sessionId)
				->delete()
			;
		}

		$context->addResponseHeader('Set-Cookie', '');

		return [
			'status' => 'OK'
		];
	}
);

// BFF редиректит сюда если пользователь не авторизован
$app->addRoute(
	HttpMethod::GET,
	'/signin',
	static function (Context $context): array
	{
		return [
			'message' => 'Please login!'
		];
	}
);

$app->addRoute(
	HttpMethod::POST,
	'/login',
	static function (Context $context): array
	{
		$data = $context->getRequest()->getParsedBody();
		$login = isset($data['login']) ? (string)$data['login'] : '';
		$password = isset($data['password']) ? (string)$data['password'] : '';

		$error = null;
		if ($login === '')
		{
			return [
				'error' => 'Where is your login?'
			];
		}
		$user = \AuthService\User::getByLogin($login);
		if ($user && $user->checkPassword($password))
		{
			$session = \AuthService\Session::createForUser($user);
			$context->addResponseHeader('Set-Cookie', 'session=' . $session->getSessionId());

			return [
				'status' => 'OK'
			];
		}
		return [
			'error' => 'User not found or password incorrect'
		];
	}
);

$app->addRoute(
	HttpMethod::POST,
	'/user/register',
	static function (Context $context): array
	{
		$data = $context->getRequest()->getParsedBody();

		$login = isset($data['login']) ? (string)$data['login'] : '';
		$password = isset($data['password']) ? (string)$data['password'] : '';
		$clientId = isset($data['clientId']) ? (int)$data['clientId'] : 0;

		$error = null;
		if ($login === '')
		{
			return [
				'error' => 'Login is слишком пустой'
			];
		}
		if ($password === '')
		{
			return [
				'error' => 'Password is too simple'
			];
		}

		// обеспечение идемпотентности при регистрации пользователя
		$requestId = '';
		if ($context->getRequest()->hasHeader('X-Request-Id'))
		{
			$requestId = $context->getRequest()->getHeaderLine('X-Request-Id');
			$user = \AuthService\User::getByIdempotenceKey($requestId);
			if ($user)
			{
				return $user->toArray();
			}
		}

		if(\AuthService\User::getByLogin($login))
		{
			return [
				'error' => 'User with this login already exists'
			];
		}

		$user = \AuthService\User::create(
			$login,
			$password,
			$clientId,
			$requestId
		);
		if ($user)
		{
			return $user->toArray();
		}

		return [
			'error' => 'User was not registered'
		];
	}
);

$app->run();
