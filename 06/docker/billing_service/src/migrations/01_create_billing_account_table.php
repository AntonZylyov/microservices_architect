<?php
require __DIR__ . '/../vendor/autoload.php';

$config = \BillingServiceApp\Config::getInstance();
$config->initFromEnv();

$db = \BillingServiceApp\Database::getInstance();
$db->setConnectionParams(
	$config->getDatabaseDsn(),
	$config->getDatabaseUsername(),
	$config->getDatabasePassword()
);

$db->query("
	CREATE TABLE `billing_accounts` (
	  `id` int unsigned NOT NULL AUTO_INCREMENT,
	  `userId` int unsigned NOT NULL,
	  `balance` decimal NOT NULL,
	  PRIMARY KEY (`id`),
	  UNIQUE INDEX ix_user_id (userId) 
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");