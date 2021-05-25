<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$startTime = microtime(true);

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

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

$app->get('/metrics', static function (Request $request, Response $response, $args) use ($prometheusRegistry)  {
	$render = new Prometheus\RenderTextFormat();
	$result = $render->render($prometheusRegistry->getMetricFamilySamples());
	$response->getBody()->write($result);
	return $response
		->withHeader('Content-Type', Prometheus\RenderTextFormat::MIME_TYPE);
});

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->forceContentType('application/json');
$errorHandler->registerErrorRenderer('application/json', \MyApp\ErrorHandler::class);

$app->run();

$prometheusRegistry
	->getOrRegisterCounter('myapp', 'app_request_count', 'Request counter', ['method', 'endpoint', 'http_status'])
	->inc([$_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"], http_response_code()]);

$finishTime = microtime(true);
$prometheusRegistry
	->getOrRegisterHistogram('myapp', 'app_request_latency_msec', 'Request latency', ['method', 'endpoint'],
		[1, 10, 100, 200, 300, 400, 500, 600, 700, 800, 900, 1000])
	->observe(round(($finishTime - $startTime) * 1000), [$_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"]]);