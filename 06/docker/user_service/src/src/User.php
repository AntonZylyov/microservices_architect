<?php

namespace UserServiceApp;

class User
{
	protected $id = 0;
	protected $firstName = '';
	protected $lastName = '';
	protected $email = '';

	private const tableName = 'users';

	public static function create(
		string $firstName,
		string $lastName,
		string $email
	)
	{
		$user = new self();
		$user
			->setFirstName($firstName)
			->setLastName($lastName)
			->setEmail($email)
			->save();

		return $user;
	}

	public static function getById(int $id): ?self
	{

		$fields = Database::getInstance()->selectOne(self::tableName, "id = ?", [$id]);
		if ($fields)
		{
			$user = new self();
			$user
				->setId($fields['id'])
				->setFirstName($fields['firstName'])
				->setLastName($fields['lastName'])
				->setEmail($fields['email']);
			return $user;
		}
		return null;
	}


	public static function getByEmail(string $email): ?self
	{

		$fields = Database::getInstance()->selectOne(self::tableName, "email = ?", [$email]);
		if ($fields)
		{
			$user = new self();
			$user
				->setId($fields['id'])
				->setFirstName($fields['firstName'])
				->setLastName($fields['lastName'])
				->setEmail($fields['email']);
			return $user;
		}
		return null;
	}

	public static function delete(int $id): void
	{
		Database::getInstance()->delete(self::tableName, $id);
	}

	/**
	 * @return int
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * @param mixed $id
	 * @return User
	 */
	protected function setId($id): self
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFirstName(): string
	{
		return $this->firstName;
	}

	/**
	 * @param string $firstName
	 * @return User
	 */
	public function setFirstName(string $firstName): self
	{
		$this->firstName = $firstName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLastName(): string
	{
		return $this->lastName;
	}

	/**
	 * @param string $lastName
	 * @return User
	 */
	public function setLastName(string $lastName): self
	{
		$this->lastName = $lastName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEmail(): string
	{
		return $this->email;
	}

	/**
	 * @param string $email
	 * @return User
	 */
	public function setEmail(string $email): self
	{
		$this->email = $email;
		return $this;
	}

	public function save(): void
	{
		$fields = [
			'firstName' => $this->getFirstName(),
			'lastName' => $this->getLastName(),
			'email' => $this->getEmail()
		];
		if ($this->id > 0)
		{
			Database::getInstance()->update(self::tableName, $this->id, $fields);
		}
		else
		{
			$this->id = Database::getInstance()->add(self::tableName, $fields);
		}
	}
}