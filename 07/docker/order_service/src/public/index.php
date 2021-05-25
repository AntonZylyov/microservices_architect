<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$config = \OrderServiceApp\Config::getInstance();
$config->initFromEnv();

$db = \OrderServiceApp\Database::getInstance();
$db->setConnectionParams(
	$config->getDatabaseDsn(),
	$config->getDatabaseUsername(),
	$config->getDatabasePassword()
);

$app = AppFactory::create();

$app->addRoutingMiddleware();

$app->get('/', static function (Request $request, Response $response, $args) {
	$result = [
		'result' => 'Order service'
	];
	return \OrderServiceApp\Response::createJsonResponse($response, $result);
});

$app->get('/health', static function (Request $request, Response $response, $args) {
	$result = [
		'status' => 'OK'
	];
	return \OrderServiceApp\Response::createJsonResponse($response, $result);
});

$app->get('/orders', static function (Request $request, Response $response, $args) {

	$result = [
		'data' => []
	];
	foreach (\OrderServiceApp\Order::getList() as $order)
	{
		/** @var $order \OrderServiceApp\Order */
		$result['data'][] = [
			'id' => $order->getId(),
			'userId' => $order->getUserId(),
			'sum' => $order->getSum()
		];
	}
	$lastOrder = \OrderServiceApp\Order::getLast();
	return
		\OrderServiceApp\Response::createJsonResponse($response, $result)
		->withHeader('X-Etag', $lastOrder ? $lastOrder->getId() : 0);
});

$app->post('/order/create', static function (Request $request, Response $response, $args) {
	if($request->hasHeader('X-If-Match'))
	{
		$lastOrder = \OrderServiceApp\Order::getLast();
		$lastOrderId = (string)($lastOrder ? $lastOrder->getId() : 0);
		$eTag = (string)($request->getHeader('X-If-Match')[0]);
		if ($eTag !== $lastOrderId)
		{
			$result = [
				'error' => 'Idempotence check failed'
			];
			return \OrderServiceApp\Response::createJsonResponse($response, $result)
				->withStatus(429, 'Conflict');
		}
	}

	$postData = file_get_contents('php://input');

	$data = json_decode($postData, true);
	$data = is_array($data) ? $data : [];

	$userId = (int)$data['userId'];
	if ($userId <= 0)
	{
		$result = [
			'error' => 'Wrong user'
		];
		return \OrderServiceApp\Response::createJsonResponse($response, $result);
	}
	$sum = (float)$data['sum'];
	if ($sum <= 0)
	{
		$result = [
			'error' => 'Wrong sum'
		];
		return \OrderServiceApp\Response::createJsonResponse($response, $result);
	}


	$billingService = new \OrderServiceApp\BillingService();
	$notificationService = new \OrderServiceApp\NotificationService();
	$withdrawResult = $billingService->withdraw($userId, $sum);
	if (isset($withdrawResult['error']))
	{
		$result = [
			'error' => 'Can not create order: '.$withdrawResult['error']
		];
		$notificationService->notifyNewOrderFailure($userId, $sum);
	}
	else
	{
		$order = \OrderServiceApp\Order::create(
			$userId,
			$sum
		);
		$notificationService->notifyNewOrderSuccess($order->getId(), $userId, $sum);

		$result = [
			'success' => 'ok'
		];
	}

	return \OrderServiceApp\Response::createJsonResponse($response, $result);
});

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->forceContentType('application/json');
$errorHandler->registerErrorRenderer('application/json', \OrderServiceApp\ErrorHandler::class);

$app->run();