<?php

namespace AuthApp;

use Slim\Interfaces\ErrorRendererInterface;

class ErrorHandler implements ErrorRendererInterface
{
	public function __invoke(\Throwable $exception, bool $displayErrorDetails): string
	{
		return json_encode(['error' => $exception->getMessage()]);
	}
}