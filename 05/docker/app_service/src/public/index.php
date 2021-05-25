<?php

header("Content-type: application/json; charset=utf-8");

$userId = isset($_SERVER['HTTP_X_USERID']) ? (int)$_SERVER['HTTP_X_USERID'] : 0;

if ($userId > 0)
{
	$data = [
		'id' => $userId,
		'name' => (string)($_SERVER['HTTP_X_USERFIRSTNAME'] ?? ''),
		'lastName' => (string)($_SERVER['HTTP_X_USERLASTNAME'] ?? ''),
		'email' => (string)($_SERVER['HTTP_X_USEREMAIL'] ?? ''),
	];
}
else
{
	$data = ['result' => 'error'];
}

echo json_encode($data);