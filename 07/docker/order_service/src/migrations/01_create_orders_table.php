<?php
require __DIR__ . '/../vendor/autoload.php';


$config = \OrderServiceApp\Config::getInstance();
$config->initFromEnv();

$db = \OrderServiceApp\Database::getInstance();
$db->setConnectionParams(
	$config->getDatabaseDsn(),
	$config->getDatabaseUsername(),
	$config->getDatabasePassword()
);

$db->query("
	CREATE TABLE `orders` (
	  `id` int unsigned NOT NULL AUTO_INCREMENT,
	  `userId` int unsigned NOT NULL,
	  `sum` decimal NOT NULL,
	  PRIMARY KEY (`id`) 
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");