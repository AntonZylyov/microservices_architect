<?php
$uri = rtrim($_SERVER['REQUEST_URI'], '/');

if ($uri === '/health')
{
	header("Content-type: application/json; charset=utf-8");
	echo json_encode([
		'status' => 'OK'
	]);
}
else
{
	echo 'Nothing here...';
}