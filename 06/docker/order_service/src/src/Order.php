<?php

namespace OrderServiceApp;

class Order
{
	protected $id = 0;
	protected $userId = '';
	protected $sum = '';

	private const tableName = 'orders';

	public static function create(
		int $userId,
		float $sum
	)
	{
		$order = new self();
		$order
			->setUserId($userId)
			->setSum($sum)
			->save();

		return $order;
	}

	public static function getById(int $id): ?self
	{

		$fields = Database::getInstance()->selectOne(self::tableName, "id = ?", [$id]);
		if ($fields)
		{
			$order = new self();
			$order
				->setId($fields['id'])
				->setUserId($fields['userId'])
				->setSum($fields['sum']);
			return $order;
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
	 * @return int
	 */
	public function getUserId(): int
	{
		return $this->userId;
	}

	/**
	 * @param int $userId
	 * @return Order
	 */
	public function setUserId(int $userId): self
	{
		$this->userId = $userId;
		return $this;
	}

	/**
	 * @return float
	 */
	public function getSum(): float
	{
		return $this->sum;
	}

	/**
	 * @param float $sum
	 * @return Order
	 */
	public function setSum(float $sum): self
	{
		$this->sum = $sum;
		return $this;
	}

	public function save(): void
	{
		$fields = [
			'userId' => $this->getUserId(),
			'sum' => $this->getSum()
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