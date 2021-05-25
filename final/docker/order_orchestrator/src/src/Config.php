<?php

namespace OrderOrchestrator;

class Config extends \BaseMicroservice\Config
{
	public function getBillingServiceHost(): string
	{
		return $this->getValue('BILLING_SERVICE_HOST');
	}

	public function getOrderServiceHost(): string
	{
		return $this->getValue('ORDER_SERVICE_HOST');
	}

	public function getNotificationServiceHost(): string
	{
		return $this->getValue('NOTIFICATION_SERVICE_HOST');
	}
}
