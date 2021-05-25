<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$config = \BillingServiceApp\Config::getInstance();
$config->initFromEnv();

$db = \BillingServiceApp\Database::getInstance();
$db->setConnectionParams(
	$config->getDatabaseDsn(),
	$config->getDatabaseUsername(),
	$config->getDatabasePassword()
);

$app = AppFactory::create();

$app->addRoutingMiddleware();

$app->get('/', static function (Request $request, Response $response, $args) {
	$result = [
		'result' => 'Billing service'
	];
	return \BillingServiceApp\Response::createJsonResponse($response, $result);
});

$app->get('/health', static function (Request $request, Response $response, $args) {
	$result = [
		'status' => 'OK'
	];
	return \BillingServiceApp\Response::createJsonResponse($response, $result);
});

$app->post('/billing/createAccount', static function (Request $request, Response $response, $args) {
	$postData = file_get_contents('php://input');

	$data = json_decode($postData, true);
	$data = is_array($data) ? $data : [];

	$existedAccount = \BillingServiceApp\Account::getByUserId((int)$data['userId']);
	if ($existedAccount)
	{
		$result = [
			'error' => 'Account already exists'
		];
	}
	else
	{
		$account = \BillingServiceApp\Account::create(
			(int)$data['userId'],
			(int)$data['initialSum']
		);
		if ($account)
		{
			$result = [
				"id" => $account->getId(),
				"userId" => $account->getUserId(),
				"balance" => $account->getBalance()
			];
		}
		else
		{
			$result = [
				'error' => 'User not found'
			];
		}
	}

	return \BillingServiceApp\Response::createJsonResponse($response, $result);
});

$app->get('/billing/{userId}', static function (Request $request, Response $response, $args) {

	$account = \BillingServiceApp\Account::getByUserId((int)$args['userId']);
	if ($account)
	{
		$result = [
			"id" => $account->getId(),
			"userId" => $account->getUserId(),
			"balance" => $account->getBalance()
		];
	}
	else
	{
		$result = [
			'error' => 'User not found'
		];
	}
	return \BillingServiceApp\Response::createJsonResponse($response, $result);
});

$app->post('/billing/withdraw', static function (Request $request, Response $response, $args) {
	$postData = file_get_contents('php://input');

	$data = json_decode($postData, true);
	$data = is_array($data) ? $data : [];

	$sum = (float)$data['sum'];
	if ($sum <= 0)
	{
		$result = [
			'error' => 'Wrong sum'
		];
		return \BillingServiceApp\Response::createJsonResponse($response, $result);
	}

	$db = \BillingServiceApp\Database::getInstance();

	$db->beginTransaction();
	$account = \BillingServiceApp\Account::getByUserId((int)$data['userId']);
	if (!$account)
	{
		$db->rollback();
		$result = [
			'error' => 'Account not found'
		];
		return \BillingServiceApp\Response::createJsonResponse($response, $result);
	}

	if ($account->getBalance() < $sum)
	{
		$db->rollback();
		$result = [
			'error' => 'Денег нет но вы держитесь'
		];
		return \BillingServiceApp\Response::createJsonResponse($response, $result);
	}
	$account->addToBalance(-$sum);

	$db->commit();

	$result = [
		'success' => 'ok'
	];

	return \BillingServiceApp\Response::createJsonResponse($response, $result);
});

$app->post('/billing/deposit', static function (Request $request, Response $response, $args) {
	$postData = file_get_contents('php://input');

	$data = json_decode($postData, true);
	$data = is_array($data) ? $data : [];

	$sum = (float)$data['sum'];
	if ($sum <= 0)
	{
		$result = [
			'error' => 'Wrong sum'
		];
		return \BillingServiceApp\Response::createJsonResponse($response, $result);
	}

	$db = \BillingServiceApp\Database::getInstance();

	$db->beginTransaction();
	$account = \BillingServiceApp\Account::getByUserId((int)$data['userId']);
	if (!$account)
	{
		$db->rollback();
		$result = [
			'error' => 'Account not found'
		];
		return \BillingServiceApp\Response::createJsonResponse($response, $result);
	}

	$account->addToBalance($sum);

	$db->commit();

	$result = [
		'success' => 'ok'
	];

	return \BillingServiceApp\Response::createJsonResponse($response, $result);
});


$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->forceContentType('application/json');
$errorHandler->registerErrorRenderer('application/json', \BillingServiceApp\ErrorHandler::class);

$app->run();