<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$config = \NotificationServiceApp\Config::getInstance();
$config->initFromEnv();

$db = \NotificationServiceApp\Database::getInstance();
$db->setConnectionParams(
	$config->getDatabaseDsn(),
	$config->getDatabaseUsername(),
	$config->getDatabasePassword()
);

$app = AppFactory::create();

$app->addRoutingMiddleware();

$app->get('/', static function (Request $request, Response $response, $args) {
	$result = [
		'result' => 'Notification service'
	];
	return \NotificationServiceApp\Response::createJsonResponse($response, $result);
});

$app->get('/health', static function (Request $request, Response $response, $args) {
	$result = [
		'status' => 'OK'
	];
	return \NotificationServiceApp\Response::createJsonResponse($response, $result);
});

$app->post('/notify/newOrderSuccess', static function (Request $request, Response $response, $args) {
	$postData = file_get_contents('php://input');

	$data = json_decode($postData, true);
	$data = is_array($data) ? $data : [];

	$userService = new \NotificationServiceApp\UserService();
	$data['userInfo'] = $userService->getById((int)$data['userId']);

	$notification = new \NotificationServiceApp\Notification();
	$notification
		->setType('newOrderSuccess')
		->setData($data)
		->save();

	$result = [
		'success' => 'ok'
	];

	return \NotificationServiceApp\Response::createJsonResponse($response, $result);
});

$app->post('/notify/newOrderFailure', static function (Request $request, Response $response, $args) {
	$postData = file_get_contents('php://input');

	$data = json_decode($postData, true);
	$data = is_array($data) ? $data : [];

	$userService = new \NotificationServiceApp\UserService();
	$data['userInfo'] = $userService->getById((int)$data['userId']);

	$notification = new \NotificationServiceApp\Notification();
	$notification
		->setType('newOrderFailure')
		->setData($data)
		->save();

	$result = [
		'success' => 'ok'
	];

	return \NotificationServiceApp\Response::createJsonResponse($response, $result);
});

$app->get('/notification/last', static function (Request $request, Response $response, $args) {

	$notification = \NotificationServiceApp\Notification::getLast();
	if ($notification)
	{
		$result = [
			'type' => $notification->getType(),
			'data' => $notification->getData()
		];
	}
	else
	{
		$result = [
			'error' => 'Notifications not found'
		];
	}

	return \NotificationServiceApp\Response::createJsonResponse($response, $result);
});

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->forceContentType('application/json');
$errorHandler->registerErrorRenderer('application/json', \NotificationServiceApp\ErrorHandler::class);

$app->run();