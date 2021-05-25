<?php

namespace NotificationService;

class Config extends \BaseMicroservice\Config
{
	public function getClientServiceHost(): string
	{
		return $this->getValue('CLIENT_SERVICE_HOST');
	}
}
