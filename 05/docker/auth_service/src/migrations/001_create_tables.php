<?php
require __DIR__ . '/../vendor/autoload.php';

$mysqlHost = $_ENV['DATABASE_HOST'] . ($_ENV['DATABASE_PORT'] ? ':'.$_ENV['DATABASE_PORT'] : '');
$mysqlDbName = (string)$_ENV['DATABASE_NAME'];

\AuthApp\Database::setConnectionParams(
	"mysql:host=$mysqlHost;dbname=$mysqlDbName",
	(string)$_ENV['DATABASE_USERNAME'],
	(string)$_ENV['DATABASE_PASSWORD']
);

\AuthApp\Database::query("
	CREATE TABLE `users` (
	  `id` int unsigned NOT NULL AUTO_INCREMENT,
	  `login` varchar(255) DEFAULT '',
	  `password` varchar(255) DEFAULT '',
	  `firstName` varchar(255) DEFAULT '',
	  `lastName` varchar(255) DEFAULT '',
	  `email` varchar(255) DEFAULT '',
	  PRIMARY KEY (`id`),
	  UNIQUE INDEX ix_login(`login`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

\AuthApp\Database::query("
	CREATE TABLE `sessions` (
	  `id` int unsigned NOT NULL AUTO_INCREMENT,
	  `userId` int unsigned NOT NULL,
	  `sessionId` varchar(255) DEFAULT '',
	  `deadline` datetime NOT NULL,
	  PRIMARY KEY (`id`),
	  UNIQUE INDEX ix_session(`sessionId`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");