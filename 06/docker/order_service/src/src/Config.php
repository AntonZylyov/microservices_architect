<?php

namespace OrderServiceApp;


class Config
{
	protected $config = [];
	protected static $instance;

	public static function getInstance(): self
	{
		if (!self::$instance)
		{
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function __construct(array $config = [])
	{
		$this->config = $config;
	}

	public function initFromEnv(): void
	{
		$this->config = $_ENV;
	}

	public function getDatabaseDsn(): string
	{
		$host = $this->getValue('DATABASE_HOST');
		$port = $this->getValue('DATABASE_PORT');
		$mysqlHost = $host . ($port ? ':' . $port : '');
		$mysqlDbName = $this->getValue('DATABASE_NAME');

		return "mysql:host=$mysqlHost;dbname=$mysqlDbName";
	}

	public function getDatabaseUsername(): string
	{
		return $this->getValue('DATABASE_USERNAME');
	}

	public function getDatabasePassword(): string
	{
		return $this->getValue('DATABASE_PASSWORD');
	}

	public function getBillingServiceHost(): string
	{
		return $this->getValue('BILLING_SERVICE_HOST');
	}

	public function getNotificationServiceHost(): string
	{
		return $this->getValue('NOTIFICATION_SERVICE_HOST');
	}

	protected function getValue(string $key): string
	{
		return (string)($this->config[$key] ?? '');
	}
}