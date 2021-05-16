<?php
require __DIR__ . '/../vendor/autoload.php';

\BaseMicroservice\Database::getInstance()->init(\BaseMicroservice\Config::createFromEnv());

\BaseMicroservice\Database::getInstance()->query("
	CREATE TABLE `clients` (
	  `id` int unsigned NOT NULL AUTO_INCREMENT,
	  `idempotenceKey` varchar(255) DEFAULT '',
	  `firstName` varchar(255) DEFAULT '',
	  `lastName` varchar(255) DEFAULT '',
	  `email` varchar(255) DEFAULT '',
	  `created` datetime default NOW(),
	  `isPending` tinyint DEFAULT 1,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");


