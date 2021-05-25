<?php

namespace AuthApp;

class Session
{
	private const tableName = 'sessions';

	public static function getNewSessionForUser(User $user): string
	{
		do
		{
			$sessionId = bin2hex(random_bytes(32));
		}
		while (static::getUserBySessionId($sessionId));

		$fields = [
			'userId' => $user->getId(),
			'sessionId' => $sessionId,
			'deadline' => date('Y-m-d H:i:s', time() + 60*60*24) // 1 day
		];
		Database::add(self::tableName, $fields);

		return $sessionId;
	}

	public static function getUserBySessionId(string $sessionId):? User
	{
		if ($sessionId === '')
		{
			return null;
		}

		$fields = Database::selectOne(self::tableName, "sessionId = ?", [$sessionId]);
		if ($fields)
		{
			// @todo Проверять deadline
			if ($fields['userId'])
			{
				return User::getById($fields['userId']);
			}
		}
		return  null;
	}

	public static function deleteSessionById(string $sessionId): void
	{
		$fields = Database::selectOne(self::tableName, "sessionId = ?", [$sessionId]);
		if ($fields)
		{
			Database::delete(self::tableName, (int)$fields['id']);
		}
	}


}