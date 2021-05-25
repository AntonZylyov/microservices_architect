<?php

namespace ClientService;

use BaseMicroservice\IdempotentEntity;

class Client extends IdempotentEntity
{
	public static function create(
		string $firstName,
		string $lastName,
		string $email,
		string $idempotenceKey = ''
	): self
	{
		$client = new self();
		$client
			->setFirstName($firstName)
			->setLastName($lastName)
			->setEmail($email)
			->setIsPending(true)
			->setIdempotenceKey($idempotenceKey)
			->save();

		return $client;
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
	 * @return string
	 */
	public function getFirstName(): ?string
	{
		return $this->getFieldValue('firstName');
	}

	/**
	 * @param string $firstName
	 * @return self
	 */
	public function setFirstName(string $firstName): self
	{
		$this->setFieldValue('firstName', $firstName);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLastName(): ?string
	{
		return $this->getFieldValue('lastName');
	}

	/**
	 * @param string $lastName
	 * @return self
	 */
	public function setLastName(string $lastName): self
	{
		$this->setFieldValue('lastName', $lastName);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEmail(): ?string
	{
		return $this->getFieldValue('email');
	}

	/**
	 * @param string $email
	 * @return self
	 */
	public function setEmail(string $email): self
	{
		$this->setFieldValue('email', $email);
		return $this;
	}

	/**
	 * @return bool
	 */
	public function getIsPending(): bool
	{
		return (bool)$this->getFieldValue('isPending');
	}

	/**
	 * @param bool $isPending
	 * @return self
	 */
	public function setIsPending(bool $isPending): self
	{
		$this->setFieldValue('isPending', $isPending);
		return $this;
	}

	protected static function getTableName(): string
	{
		return 'clients';
	}

	public static function createFromArray(array $fields): self
	{
		$client = new self();
		$client
			->setId($fields['id'] ?? null)
			->setFirstName($fields['firstName'] ?? null)
			->setLastName($fields['lastName'] ?? null)
			->setEmail($fields['email'] ?? null)
			->setIsPending((bool)$fields['isPending'])
		;

		return $client;
	}
}