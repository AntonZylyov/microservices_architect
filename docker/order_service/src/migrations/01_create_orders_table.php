<?php
require __DIR__ . '/../vendor/autoload.php';

\BaseMicroservice\Database::getInstance()->init(\BaseMicroservice\Config::createFromEnv());

\BaseMicroservice\Database::getInstance()->query("
	CREATE TABLE `orders` (
	  `id` int unsigned NOT NULL AUTO_INCREMENT,
	  `idempotenceKey` varchar(255) DEFAULT '',
	  `clientId` int unsigned NOT NULL,
	  `sum` decimal NOT NULL,
	  `created` datetime default NOW(),
	  `isPending` tinyint DEFAULT 1,
	  PRIMARY KEY (`id`) 
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
