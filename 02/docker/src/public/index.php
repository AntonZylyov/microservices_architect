<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$mysqlHost = $_ENV['DATABASE_HOST'] . ($_ENV['DATABASE_PORT'] ? ':'.$_ENV['DATABASE_PORT'] : '');
$mysqlDbName = (string)$_ENV['DATABASE_NAME'];

\MyApp\Database::setConnectionParams(
	"mysql:host=$mysqlHost;dbname=$mysqlDbName",
	(string)$_ENV['DATABASE_USERNAME'],
	(string)$_ENV['DATABASE_PASSWORD']
);

$app->addRoutingMiddleware();

$app->get('/', static function (Request $request, Response $response, $args) {
	$result = [
		'result' => 'WELCOME!'
	];
	return \MyApp\Response::createJsonResponse($response, $result);
});

$app->get('/health', static function (Request $request, Response $response, $args) {
	$result = [
		'status' => 'OK'
	];
	return \MyApp\Response::createJsonResponse($response, $result);
});

$app->get('/user/{id}', static function (Request $request, Response $response, $args) {
	$id = (int)$args['id'];
	$user = \MyApp\User::getById($id);
	if ($user)
	{
		$result = [
			"id" => $user->getId(),
			"username" => $user->getUserName(),
			"firstName" => $user->getFirstName(),
			"lastName" => $user->getLastName(),
			"email" => $user->getEmail(),
			"phone" => $user->getPhone(),
		];
	}
	else
	{
		$result = [
			'error' => 'User not found'
		];
	}
	return \MyApp\Response::createJsonResponse($response, $result);
});

$app->post('/user', static function (Request $request, Response $response, $args) {
	$postData = file_get_contents('php://input');
	$data = json_decode($postData, true);
	$data = is_array($data) ? $data : [];

	$user = \MyApp\User::create(
		(string)$data['username'],
		(string)$data['firstName'],
		(string)$data['lastName'],
		(string)$data['email'],
		(string)$data['phone']
	);
	if ($user)
	{
		$result = [
			"id" => $user->getId(),
			"username" => $user->getUserName(),
			"firstName" => $user->getFirstName(),
			"lastName" => $user->getLastName(),
			"email" => $user->getEmail(),
			"phone" => $user->getPhone(),
		];
	}
	else
	{
		$result = [
			'error' => 'User not found'
		];
	}
	return \MyApp\Response::createJsonResponse($response, $result);
});

$app->put('/user/{id}', static function (Request $request, Response $response, $args) {
	$postData = file_get_contents('php://input');
	$data = json_decode($postData, true);
	$data = is_array($data) ? $data : [];

	$user = \MyApp\User::getById((int)$args['id']);
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
	return \MyApp\Response::createJsonResponse($response, $result);
});

$app->delete('/user/{id}', static function (Request $request, Response $response, $args) {
	$id = (int)$args['id'];
	$user = \MyApp\User::getById($id);
	if ($user)
	{
		\MyApp\User::delete($id);
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
	return \MyApp\Response::createJsonResponse($response, $result);
});

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->forceContentType('application/json');
$errorHandler->registerErrorRenderer('application/json', \MyApp\ErrorHandler::class);

$app->run();