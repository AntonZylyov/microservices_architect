<?php

namespace AuthService;

use BaseMicroservice\Entity;

class Session extends Entity
{
	public static function createForUser(User $user): self
	{
		$session = new self();
		do
		{
			$session->setSessionId(bin2hex(random_bytes(32)));
		}
		while ($session->findUser());

		$session
			->setUserId($user->getId())
			->setDeadline(date('Y-m-d H:i:s', time() + 60*60*24)) // 1 day
			->save();

		return $session;
	}

	public function findUser():? User
	{
		$sessionId = $this->getSessionId();
		if (!$sessionId)
		{
			return null;
		}

		$fields = $this->db->selectOne(static::getTableName(), "sessionId = ?", [$sessionId]);
		if ($fields)
		{
			// @todo Проверять deadline сессии
			if ($fields['userId'])
			{
				return User::getById($fields['userId']);
			}
		}

		return null;
	}

	public function delete(): void
	{
		$sessionId = $this->getSessionId();
		if (!$sessionId)
		{
			return;
		}

		$fields = $this->db->selectOne(static::getTableName(), "sessionId = ?", [$sessionId]);
		if ($fields)
		{
			$this->db->delete(static::getTableName(), (int)$fields['id']);
		}
	}

	protected static function getTableName(): string
	{
		return 'sessions';
	}

	/**
	 * @return int
	 */
	public function getId(): ?int
	{
		return $this->getFieldValue('id');
	}

	/**
	 * @param mixed $id
	 * @return self
	 */
	protected function setId($id): self
	{
		$this->setFieldValue('id', $id);
		return $this;
	}

	/**
	 * @return int
	 */
	public function getUserId(): ?int
	{
		return $this->getFieldValue('userId');
	}

	/**
	 * @param mixed $userId
	 * @return self
	 */
	protected function setUserId($userId): self
	{
		$this->setFieldValue('userId', $userId);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSessionId(): ?string
	{
		return $this->getFieldValue('sessionId');
	}

	/**
	 * @param string $sessionId
	 * @return self
	 */
	public function setSessionId(string $sessionId): self
	{
		$this->setFieldValue('sessionId', $sessionId);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDeadline(): ?string
	{
		return $this->getFieldValue('deadline');
	}

	/**
	 * @param string $deadline
	 * @return self
	 */
	public function setDeadline(string $deadline): self
	{
		$this->setFieldValue('deadline', $deadline);
		return $this;
	}

	public static function createFromArray(array $fields): self
	{
		$user = new self();
		$user
			->setId($fields['id'] ?? null)
			->setSessionId($fields['sessionId'] ?? null)
			->setDeadline($fields['deadline'] ?? null)
		;

		return $user;
	}
}
