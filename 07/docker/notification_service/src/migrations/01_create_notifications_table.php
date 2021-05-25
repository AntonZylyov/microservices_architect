<?php
require __DIR__ . '/../vendor/autoload.php';

$config = \NotificationServiceApp\Config::getInstance();
$config->initFromEnv();

$db = \NotificationServiceApp\Database::getInstance();
$db->setConnectionParams(
	$config->getDatabaseDsn(),
	$config->getDatabaseUsername(),
	$config->getDatabasePassword()
);

$db->query("
	CREATE TABLE `notifications` (
	  `id` int unsigned NOT NULL AUTO_INCREMENT,
	  `type` varchar(255) NOT NULL,
	  `data` text NOT NULL,
	  PRIMARY KEY (`id`) 
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");