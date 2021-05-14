<?php

namespace AuthService;

use BaseMicroservice\Database;
use BaseMicroservice\Entity;

class User extends Entity
{
	public static function create(
		string $login,
		string $password,
		int $clientId,
		string $idempotenceKey = ''
	)
	{
		$user = new self();
		$user
			->setLogin($login)
			->createPassword($password)
			->setClientId($clientId)
			->setIdempotenceKey($idempotenceKey)
			->save();

		return $user;
	}

	public static function getByLogin(string $login): ?self
	{

		$fields = Database::getInstance()->selectOne(static::getTableName(), "login = ?", [$login]);
		if ($fields)
		{
			return static::createFromArray($fields);
		}
		return null;
	}

	public static function getByIdempotenceKey(string $idempotenceKey): ?self
	{
		$fields = Database::getInstance()->selectOne(
			static::getTableName(),
			'idempotenceKey = ? AND created > ?',
			[
				$idempotenceKey,
				date('Y-m-d H:i:s', time() - 60) // создано меньше минуты назад
			]);
		if ($fields)
		{
			return static::createFromArray($fields);
		}
		return null;
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
	 * @return User
	 */
	protected function setId($id): self
	{
		$this->setFieldValue('id', $id);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLogin(): ?string
	{
		return $this->getFieldValue('login');
	}

	/**
	 * @param string $login
	 * @return User
	 */
	public function setLogin(string $login): self
	{
		$this->setFieldValue('login', $login);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPassword(): ?string
	{
		return $this->getFieldValue('password');
	}

	/**
	 * @param string $password
	 * @return User
	 */
	public function setPassword(string $password): self
	{
		$this->setFieldValue('password', $password);
		return $this;
	}

	public function createPassword(string $password): self
	{
		$this->setPassword(password_hash($password, PASSWORD_DEFAULT));
		return $this;
	}

	public function checkPassword(string $passwordToCheck): bool
	{
		return password_verify($passwordToCheck, $this->getPassword());
	}

	/**
	 * @return int
	 */
	public function getClientId(): ?int
	{
		return $this->getFieldValue('clientId');
	}

	/**
	 * @param int $clientId
	 * @return User
	 */
	public function setClientId(int $clientId): self
	{
		$this->setFieldValue('clientId', $clientId);
		return $this;
	}

	protected function getFieldsForSave(): array
	{
		$fields = parent::getFieldsForSave();
		if (isset($fields['id']) && $fields['id'] > 0)
		{
			unset($fields['password']);
		}

		return $fields;
	}

	public function toArray(): array
	{
		$fields = parent::toArray();
		unset($fields['password']);

		return $fields;
	}

	protected static function getTableName(): string
	{
		return 'users';
	}

	public static function createFromArray(array $fields): self
	{
		$user = new self();
		$user
			->setId($fields['id'] ?? null)
			->setLogin($fields['login'] ?? null)
			->setPassword($fields['password'] ?? null)
			->setClientId($fields['clientId'] ?? null)
		;

		return $user;
	}
}