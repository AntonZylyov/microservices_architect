<?php

namespace AuthApp;

class User
{
	protected $id = 0;
	protected $login = '';
	protected $password = '';
	protected $firstName = '';
	protected $lastName = '';
	protected $email = '';

	private const tableName = 'users';

	public static function create(
		string $login,
		string $password,
		string $firstName,
		string $lastName,
		string $email
	)
	{
		$user = new self();
		$user
			->setLogin($login)
			->createPassword($password)
			->setFirstName($firstName)
			->setLastName($lastName)
			->setEmail($email)
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
				->setLogin($fields['login'])
				->setPassword($fields['password'])
				->setFirstName($fields['firstName'])
				->setLastName($fields['lastName'])
				->setEmail($fields['email']);
			return $user;
		}
		return null;
	}


	public static function getByLogin(string $login): ?self
	{

		$fields = Database::selectOne(self::tableName, "login = ?", [$login]);
		if ($fields)
		{
			$user = new self();
			$user
				->setId($fields['id'])
				->setLogin($fields['login'])
				->setPassword($fields['password'])
				->setFirstName($fields['firstName'])
				->setLastName($fields['lastName'])
				->setEmail($fields['email']);
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
	public function getLogin(): ?string
	{
		return $this->login;
	}

	/**
	 * @param string $login
	 * @return User
	 */
	public function setLogin(string $login): self
	{
		$this->login = $login;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getPassword(): ?string
	{
		return $this->password;
	}

	/**
	 * @param string $password
	 * @return User
	 */
	public function setPassword(string $password): self
	{
		$this->password = $password;
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
			'login' => $this->getLogin(),
			'password' => $this->getPassword(),
			'firstName' => $this->getFirstName(),
			'lastName' => $this->getLastName(),
			'email' => $this->getEmail()
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