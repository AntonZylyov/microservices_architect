<?php
require __DIR__ . '/../vendor/autoload.php';

\BaseMicroservice\Database::getInstance()->init(\BaseMicroservice\Config::createFromEnv());

\BaseMicroservice\Database::getInstance()->query("
	CREATE TABLE `users` (
	  `id` int unsigned NOT NULL AUTO_INCREMENT,
	  `idempotenceKey` varchar(255) DEFAULT '',
	  `login` varchar(255) DEFAULT '',
	  `password` varchar(255) DEFAULT '',
	  `created` datetime default NOW(),
	  `clientId` int,
	  PRIMARY KEY (`id`),
	  UNIQUE INDEX ix_login(`login`),
	  INDEX ix_requestId(`idempotenceKey`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

\BaseMicroservice\Database::getInstance()->query("
	CREATE TABLE `sessions` (
	  `id` int unsigned NOT NULL AUTO_INCREMENT,
	  `userId` int unsigned NOT NULL,
	  `sessionId` varchar(255) DEFAULT '',
	  `deadline` datetime NOT NULL,
	  PRIMARY KEY (`id`),
	  UNIQUE INDEX ix_session(`sessionId`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
