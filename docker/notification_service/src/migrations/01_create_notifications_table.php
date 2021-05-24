<?php
require __DIR__ . '/../vendor/autoload.php';

\BaseMicroservice\Database::getInstance()->init(\BaseMicroservice\Config::createFromEnv());

\BaseMicroservice\Database::getInstance()->query("
	CREATE TABLE `notifications` (
	  `id` int unsigned NOT NULL AUTO_INCREMENT,
	  `idempotenceKey` varchar(255) DEFAULT '',
	  `created` datetime default NOW(),
	  `type` varchar(255) NOT NULL,
	  `data` text NOT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
