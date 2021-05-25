<?php

namespace BillingService;

use BaseMicroservice\Database;
use BaseMicroservice\IdempotentEntity;

class Account extends IdempotentEntity
{
	public static function create(
		int $clientId,
		string $idempotenceKey = ''
	)
	{
		$account = new self();
		$account
			->setClientId($clientId)
			->setIdempotenceKey($idempotenceKey)
			->save();

		return $account;
	}

	public static function getByClientId(int $clientId): ?self
	{
		$fields = Database::getInstance()->selectOne(static::getTableName(), "clientId = ?", [$clientId]);
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
	public function getClientId(): ?int
	{
		return $this->getFieldValue('clientId');
	}

	/**
	 * @param int $clientId
	 * @return self
	 */
	public function setClientId(int $clientId): self
	{
		$this->setFieldValue('clientId', $clientId);
		return $this;
	}

	/**
	 * @return float
	 */
	public function getBalance(): ?float
	{
		return $this->getFieldValue('balance');
	}

	/**
	 * @param float $balance
	 * @return self
	 */
	public function setBalance(float $balance): self
	{
		$this->setFieldValue('balance', $balance);
		return $this;
	}

	/**
	 * @param float sum
	 * @return self
	 */
	public function addToBalance(float $sum): self
	{
		if ($this->getId() > 0)
		{
			$this->setBalance( $this->getBalance() + $sum);
			$this->save();
		}
		else
		{
			throw new \Exception('Нельзя так делать');
		}

		return $this;
	}


	protected static function getTableName(): string
	{
		return 'billing_accounts';
	}

	public static function createFromArray(array $fields): self
	{
		$account = new self();
		$account
			->setId($fields['id'] ?? null)
			->setClientId($fields['clientId'] ?? null)
			->setBalance($fields['balance'] ?? null)
		;

		return $account;
	}
}
