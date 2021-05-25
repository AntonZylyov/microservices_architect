<?php


namespace AuthApp;


class Database
{
	protected static $dsn = '';
	protected static $username = '';
	protected static $password = '';

	protected static $connection;

	public static function add(string $tableName, array $fields): ?int
	{
		$sql = "INSERT INTO ".$tableName.
			" (".implode(', ', array_keys($fields)).") ". // @todo fix possible sql injection in field names
			"VALUES (".implode(', ', array_fill(0, count($fields), '?')). ")";

		$statement = self::getConnection()->prepare($sql);
		$statement->execute(array_values($fields));

		return self::getConnection()->lastInsertId();
	}

	public static function update(string $tableName, int $id, array $fields): void
	{
		if (empty($fields))
		{
			return;
		}
		$sqlFields = [];
		$replacements = [];
		foreach ($fields as $fieldName => $fieldValue)
		{
			$sqlFields[] = "`$fieldName` = ?";
			$replacements[] = (string)$fieldValue;
		}
		$replacements[] = $id;
		$sql = "UPDATE ".$tableName." SET ".implode(', ', $sqlFields)." WHERE id = ?";
		$statement = self::getConnection()->prepare($sql);
		$statement->execute($replacements);
	}

	public static function delete(string $tableName, int $id): void
	{
		$sql = "DELETE FROM ".$tableName." WHERE id = ?";
		$statement = self::getConnection()->prepare($sql);
		$statement->execute([$id]);
	}

	public static function selectOne(string $tableName, string $condition, array $placeholders, array $order = []): ?array
	{
		$result = self::selectAll($tableName, $condition, $placeholders, $order);
		return $result ? $result[0] : null;
	}


	public static function selectAll(string $tableName, string $condition, array $placeholders, array $order = [], string $limit = ''): array
	{
		$result = [];
		$sql = "SELECT * FROM ".$tableName." WHERE ".$condition;
		if (!empty($order))
		{
			//@todo Order by
		}
		if ($limit !== '')
		{
			//@todo Limit
		}
		$statement = self::getConnection()->prepare($sql);
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		$statement->execute($placeholders);
		while ($row = $statement->fetch())
		{
			$result[] = $row;
		}
		return $result;
	}

	public static function query($sql)
	{
		self::getConnection()->prepare($sql)->execute();
	}

	protected static function getConnection(): \PDO
	{
		if (!self::$connection)
		{
			self::$connection = new \PDO(self::$dsn, self::$username, self::$password);
			self::$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		}
		return self::$connection;
	}
	public static function setConnectionParams(string $dsn, string $username, string $password): void
	{
		self::$dsn = $dsn;
		self::$username = $username;
		self::$password = $password;
	}
}