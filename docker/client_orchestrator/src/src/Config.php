<?php

namespace ClientOrchestrator;

class Config extends \BaseMicroservice\Config
{
	public function getAuthServiceHost(): string
	{
		return $this->getValue('AUTH_SERVICE_HOST');
	}

	public function getClientServiceHost(): string
	{
		return $this->getValue('CLIENT_SERVICE_HOST');
	}

	public function getBillingServiceHost(): string
	{
		return $this->getValue('BILLING_SERVICE_HOST');
	}

	public function getNotificationServiceHost(): string
	{
		return $this->getValue('NOTIFICATION_SERVICE_HOST');
	}
}
