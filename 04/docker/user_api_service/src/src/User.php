<?php

namespace MyApp;

class User
{
	protected $id = 0;
	protected $userName = '';
	protected $firstName = '';
	protected $lastName = '';
	protected $email = '';
	protected $phone = '';

	private const tableName = 'users';

	public static function create(
		string $userName,
		string $firstName,
		string $lastName,
		string $email,
		string $phone
	)
	{
		$user = new self();
		$user
			->setUserName($userName)
			->setFirstName($firstName)
			->setLastName($lastName)
			->setEmail($email)
			->setPhone($phone)
			->save();

		return $user;
	}

	public static function getById(int $id): ?self
	{

		$fields = Database::selectOne(self::tableName, "id = ?", [$id]);
		if ($fields)
		{
			$user = new self();
			$user
				->setId($fields['id'])
				->setUserName($fields['username'])
				->setFirstName($fields['firstName'])
				->setLastName($fields['lastName'])
				->setEmail($fields['email'])
				->setPhone($fields['phone']);
			return $user;
		}
		return null;
	}

	public static function delete(int $id): void
	{
		Database::delete(self::tableName, $id);
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
	public function getUserName(): ?string
	{
		return $this->userName;
	}

	/**
	 * @param string $userName
	 * @return User
	 */
	public function setUserName(string $userName): self
	{
		$this->userName = $userName;
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

	/**
	 * @return string
	 */
	public function getPhone(): string
	{
		return $this->phone;
	}

	/**
	 * @param string $phone
	 * @return User
	 */
	public function setPhone(string $phone): self
	{
		$this->phone = $phone;
		return $this;
	}

	public function save()
	{
		$fields = [
			'username' => $this->getUserName(),
			'firstName' => $this->getFirstName(),
			'lastName' => $this->getLastName(),
			'email' => $this->getEmail(),
			'phone' => $this->getPhone()
		];
		if ($this->id > 0)
		{
			Database::update(self::tableName, $this->id, $fields);
		}
		else
		{
			$this->id = Database::add(self::tableName, $fields);
		}
	}
}