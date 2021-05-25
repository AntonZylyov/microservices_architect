<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$phpView = new PhpRenderer('../templates');
$phpView->setLayout("layout.php");

\MyApp\User::setBaseUrl($_ENV['USER_API_SERVICE_URL']);
$baseUrl = $_ENV['BASE_URL'] ?: '/';

$app->addRoutingMiddleware();

$app->get('/', static function (Request $request, Response $response, $args) use ($phpView, $baseUrl) {
	$users = [];
	foreach ((new \MyApp\RecentUser())->getIds() as $userId)
	{
		$user = (new \MyApp\User())->get($userId);
		if ($user)
		{
			$users[] = $user;
		}
		else
		{
			(new \MyApp\RecentUser())->removeId($userId);
		}
	}
	return $phpView->render($response, "index.php", [
		'baseUrl' => $baseUrl,
		'title' => 'Пользователи',
		'users' => $users
	]);
});

$app->post('/add_user/', static function (Request $request, Response $response, $args) use ($baseUrl) {
	$user = new \MyApp\User();
	$id = $user->add($request->getParsedBody());
	if ($id)
	{
		(new \MyApp\RecentUser())->addId($id);
	}
	return $response
		->withStatus(302)
		->withHeader('Location', $baseUrl);
});

$app->post('/edit_user/', static function (Request $request, Response $response, $args) use ($baseUrl) {
	$fields = $request->getParsedBody();
	$user = new \MyApp\User();
	$user->update((int)$fields['id'], $fields);
	return $response
		->withStatus(302)
		->withHeader('Location', $baseUrl);
});


$app->get('/delete_user/', static function (Request $request, Response $response, $args) use ($baseUrl) {
	$user = new \MyApp\User();
	$id = (int)$request->getQueryParams()['id'];
	if ($user->delete($id))
	{
		(new \MyApp\RecentUser())->removeId($id);
	}
	return $response
		->withStatus(302)
		->withHeader('Location', $baseUrl);
});

$app->run();
