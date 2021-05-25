<?php
require __DIR__ . '/../vendor/autoload.php';

\BaseMicroservice\Database::getInstance()->init(\BaseMicroservice\Config::createFromEnv());

\BaseMicroservice\Database::getInstance()->query("
	CREATE TABLE `billing_accounts` (
	  `id` int unsigned NOT NULL AUTO_INCREMENT,
	  `idempotenceKey` varchar(255) DEFAULT '',
	  `created` datetime default NOW(),
	  `clientId` int unsigned NOT NULL,
	  `balance` decimal NOT NULL default 0,
	  PRIMARY KEY (`id`),
	  UNIQUE INDEX ix_user_id (clientId) 
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
