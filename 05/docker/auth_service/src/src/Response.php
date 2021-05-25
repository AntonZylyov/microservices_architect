<?php

namespace AuthApp;

use Psr\Http\Message\ResponseInterface;


class Response
{
	public static function createJsonResponse(ResponseInterface $response, array $data): ResponseInterface
	{
		$response->getBody()->write(json_encode($data));
		return $response
			->withHeader('Content-Type', 'application/json');
	}
}