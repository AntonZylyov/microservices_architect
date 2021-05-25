<?php
require __DIR__ . '/../vendor/autoload.php';

$mysqlHost = $_ENV['DATABASE_HOST'] . ($_ENV['DATABASE_PORT'] ? ':'.$_ENV['DATABASE_PORT'] : '');
$mysqlDbName = (string)$_ENV['DATABASE_NAME'];

\MyApp\Database::setConnectionParams(
	"mysql:host=$mysqlHost;dbname=$mysqlDbName",
	(string)$_ENV['DATABASE_USERNAME'],
	(string)$_ENV['DATABASE_PASSWORD']
);

\MyApp\Database::query("
	CREATE TABLE `users` (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `username` varchar(255) DEFAULT '',
	  `firstName` varchar(255) DEFAULT '',
	  `lastName` varchar(255) DEFAULT '',
	  `email` varchar(255) DEFAULT '',
	  `phone` varchar(255) DEFAULT '',
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");