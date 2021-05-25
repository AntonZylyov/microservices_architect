<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$config = \UserServiceApp\Config::getInstance();
$config->initFromEnv();

$db = \UserServiceApp\Database::getInstance();
$db->setConnectionParams(
	$config->getDatabaseDsn(),
	$config->getDatabaseUsername(),
	$config->getDatabasePassword()
);

$app = AppFactory::create();

$app->addRoutingMiddleware();

$app->get('/', static function (Request $request, Response $response, $args) {
	$result = [
		'result' => 'User service'
	];
	return \UserServiceApp\Response::createJsonResponse($response, $result);
});

$app->get('/health', static function (Request $request, Response $response, $args) {
	$result = [
		'status' => 'OK'
	];
	return \UserServiceApp\Response::createJsonResponse($response, $result);
});

$app->get('/user/{id}', static function (Request $request, Response $response, $args) {
	$id = (int)$args['id'];
	$user = \UserServiceApp\User::getById($id);
	if ($user)
	{
		$result = [
			"id" => $user->getId(),
			"firstName" => $user->getFirstName(),
			"lastName" => $user->getLastName(),
			"email" => $user->getEmail(),
		];
	}
	else
	{
		$result = [
			'error' => 'User not found'
		];
	}
	return \UserServiceApp\Response::createJsonResponse($response, $result);
});

$app->post('/user/register', static function (Request $request, Response $response, $args) {
	$postData = file_get_contents('php://input');
	$data = json_decode($postData, true);
	$data = is_array($data) ? $data : [];

	$email = (string)$data['email'];
	if ($email === '')
	{
		$result = [
			'error' => 'Email is required'
		];
		return \UserServiceApp\Response::createJsonResponse($response, $result);
	}
	if (\UserServiceApp\User::getByEmail($email))
	{
		$result = [
			'error' => 'User with this email already exists'
		];
		return \UserServiceApp\Response::createJsonResponse($response, $result);
	}

	$user = \UserServiceApp\User::create(
		(string)$data['firstName'],
		(string)$data['lastName'],
		$email
	);
	if ($user)
	{
		$billingService = new \UserServiceApp\BillingService();
		$billingService->createAccount($user->getId());

		$result = [
			"id" => $user->getId(),
			"firstName" => $user->getFirstName(),
			"lastName" => $user->getLastName(),
			"email" => $user->getEmail(),
		];
	}
	else
	{
		$result = [
			'error' => 'User not found'
		];
	}
	return \UserServiceApp\Response::createJsonResponse($response, $result);
});

$app->put('/user/{id}', static function (Request $request, Response $response, $args) {
	$postData = file_get_contents('php://input');
	$data = json_decode($postData, true);
	$data = is_array($data) ? $data : [];

	$user = \UserServiceApp\User::getById((int)$args['id']);
	if ($user)
	{
		if (isset($data['username']))
		{
			$user->setUserName((string)$data['username']);
		}
		if (isset($data['firstName']))
		{
			$user->setFirstName((string)$data['firstName']);
		}
		if (isset($data['lastName']))
		{
			$user->setLastName((string)$data['lastName']);
		}
		if (isset($data['email']))
		{
			$user->setEmail((string)$data['email']);
		}
		if (isset($data['phone']))
		{
			$user->setPhone((string)$data['phone']);
		}
		$user->save();

		$result = [
			'result' => 'ok'
		];
	}
	else
	{
		$result = [
			'error' => 'User not found'
		];
	}
	return \UserServiceApp\Response::createJsonResponse($response, $result);
});

$app->delete('/user/{id}', static function (Request $request, Response $response, $args) {
	$id = (int)$args['id'];
	$user = \UserServiceApp\User::getById($id);
	if ($user)
	{
		\UserServiceApp\User::delete($id);
		$result = [
			'result' => 'ok'
		];
	}
	else
	{
		$result = [
			'error' => 'User not found'
		];
	}
	return \UserServiceApp\Response::createJsonResponse($response, $result);
});

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->forceContentType('application/json');
$errorHandler->registerErrorRenderer('application/json', \UserServiceApp\ErrorHandler::class);

$app->run();