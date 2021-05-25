<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$mysqlHost = $_ENV['DATABASE_HOST'] . ($_ENV['DATABASE_PORT'] ? ':'.$_ENV['DATABASE_PORT'] : '');
$mysqlDbName = (string)$_ENV['DATABASE_NAME'];

\AuthApp\Database::setConnectionParams(
	"mysql:host=$mysqlHost;dbname=$mysqlDbName",
	(string)$_ENV['DATABASE_USERNAME'],
	(string)$_ENV['DATABASE_PASSWORD']
);


$app = AppFactory::create();

$app->addRoutingMiddleware();

$app->get('/', static function (Request $request, Response $response, $args) {
	$result = [
		'result' => 'WELCOME!'
	];
	return \AuthApp\Response::createJsonResponse($response, $result);
});

$app->get('/health', static function (Request $request, Response $response, $args) {
	$result = [
		'status' => 'OK'
	];
	return \AuthApp\Response::createJsonResponse($response, $result);
});

$app->get('/signin', static function (Request $request, Response $response, $args) {
	$result = [
		'message' => 'Please login!'
	];
	return \AuthApp\Response::createJsonResponse($response, $result);
});

$app->any('/authorize', static function (Request $request, Response $response, $args) {
	$sessionId = (string)($request->getCookieParams()['session'] ?? '');
	if ($sessionId !== '')
	{
		$user = \AuthApp\Session::getUserBySessionId($sessionId);
		if($user)
		{
			return $response
				->withHeader('X-UserId', $user->getId())
				->withHeader('X-UserEmail', $user->getEmail())
				->withHeader('X-UserFirstName', $user->getFirstName())
				->withHeader('X-UserLastName', $user->getLastName());
		}
	}
	$response->getBody()->write('Unauthorized');

	return $response
		->withStatus(401, 'Unauthorized');
});

$app->get('/logout', static function (Request $request, Response $response, $args) {
	$sessionId = (string)($request->getCookieParams()['session'] ?? '');
	if ($sessionId !== '')
	{
		\AuthApp\Session::deleteSessionById($sessionId);
	}

	return
		$response->withHeader('Set-Cookie', '');
});

$app->any('/login', static function (Request $request, Response $response, $args) {
	$postData = file_get_contents('php://input');
	$data = json_decode($postData, true);
	$data = is_array($data) ? $data : [];
	$login = isset($data['login']) ? (string)$data['login'] : '';
	$password = isset($data['password']) ? (string)$data['password'] : '';

	$error = null;
	if ($login === '')
	{
		$error = 'Where is your login?';
	}
	else
	{
		$user = \AuthApp\User::getByLogin($data['login']);
		if ($user && $user->checkPassword($password))
		{
			$sessionId = \AuthApp\Session::getNewSessionForUser($user);
			return
				$response->withHeader('Set-Cookie', 'session=' . $sessionId);
		}

		$error = 'User not found';
	}
	return \AuthApp\Response::createJsonResponse($response, ['error' => $error]);
});


$app->post('/register', static function (Request $request, Response $response, $args) {
	$postData = file_get_contents('php://input');
	$data = json_decode($postData, true);
	$data = is_array($data) ? $data : [];

	$login = (string)($data['login'] ?? '');
	$password = (string)($data['password'] ?? '');
	$error = null;
	if ($login === '')
	{
		$error = 'Login is слишком пустой';
	}
	elseif ($password === '')
	{
		$error = 'Password is too simple';
	}
	elseif(\AuthApp\User::getByLogin($login))
	{
		$error = 'Login already exists';
	}
	else
	{
		$user = \AuthApp\User::create(
			$login,
			$password,
			(string)($data['firstName'] ?? ''),
			(string)($data['lastName'] ?? ''),
			(string)($data['email'] ?? '')
		);
		if ($user)
		{
			$result = [
				"id" => $user->getId(),
				"login" => $user->getLogin(),
				"firstName" => $user->getFirstName(),
				"lastName" => $user->getLastName(),
				"email" => $user->getEmail()
			];
		}
		else
		{
			$error = 'User not found';
		}
	}
	if ($error)
	{
		$result = [
			'error' => $error
		];
	}
	return \AuthApp\Response::createJsonResponse($response, $result);
});

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->forceContentType('application/json');
$errorHandler->registerErrorRenderer('application/json', \AuthApp\ErrorHandler::class);

$app->run();
