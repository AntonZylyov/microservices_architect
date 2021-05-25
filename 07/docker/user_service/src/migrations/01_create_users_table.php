<?php
require __DIR__ . '/../vendor/autoload.php';

$config = \UserServiceApp\Config::getInstance();
$config->initFromEnv();

$db = \UserServiceApp\Database::getInstance();
$db->setConnectionParams(
	$config->getDatabaseDsn(),
	$config->getDatabaseUsername(),
	$config->getDatabasePassword()
);

$db->query("
	CREATE TABLE `users` (
	  `id` int unsigned NOT NULL AUTO_INCREMENT,
	  `firstName` varchar(255) DEFAULT '',
	  `lastName` varchar(255) DEFAULT '',
	  `email` varchar(255) DEFAULT '',
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");