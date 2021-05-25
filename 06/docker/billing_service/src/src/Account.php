<?php

namespace BillingServiceApp;

class Account
{
	protected $id = 0;
	protected $userId = 0;
	protected $balance = 0;

	private const tableName = 'billing_accounts';

	public static function create(
		int $userId,
		int $balance
	): self
	{
		$account = new self();
		$account
			->setUserId($userId)
			->setBalance($balance)
			->save();

		return $account;
	}

	public static function getById(int $id): ?self
	{

		$fields = Database::getInstance()->selectOne(self::tableName, "id = ?", [$id]);
		if ($fields)
		{
			$account = new self();
			$account
				->setId($fields['id'])
				->setUserId($fields['userId'])
				->setBalance($fields['balance']);
			return $account;
		}
		return null;
	}

	public static function getByUserId(int $userId): ?self
	{

		$fields = Database::getInstance()->selectOne(self::tableName, "userId = ?", [$userId]);
		if ($fields)
		{
			$account = new self();
			$account
				->setId($fields['id'])
				->setUserId($fields['userId'])
				->setBalance($fields['balance']);
			return $account;
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
	 * @return self
	 */
	protected function setId($id): self
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getUserId(): int
	{
		return $this->userId;
	}

	/**
	 * @param int $userId
	 * @return self
	 */
	public function setUserId(int $userId): self
	{
		$this->userId = $userId;
		return $this;
	}

	/**
	 * @return float
	 */
	public function getBalance(): float
	{
		return $this->balance;
	}

	/**
	 * @param float $balance
	 * @return self
	 */
	public function setBalance(float $balance): self
	{
		$this->balance = $balance;
		return $this;
	}

	/**
	 * @param float sum
	 * @return self
	 */
	public function addToBalance(float $sum): self
	{
		if ($this->id > 0)
		{
			Database::getInstance()->update(self::tableName, $this->id, ['balance' => $this->getBalance() + $sum]);
		}
		else
		{
			throw new \Exception('Нельзя так делать');
		}

		return $this;
	}

	public function save(): void
	{
		$fields = [
			'userId' => $this->getUserId(),
			'balance' => $this->getBalance(),
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