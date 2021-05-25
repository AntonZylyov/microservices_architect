<?php

namespace OrderService;

use BaseMicroservice\IdempotentEntity;

class Order extends IdempotentEntity
{
	public static function create(
		int $clientId,
		float $sum,
		string $idempotenceKey = ''
	): self
	{
		$order = new self();
		$order
			->setClientId($clientId)
			->setSum($sum)
			->setIsPending(true)
			->setIdempotenceKey($idempotenceKey)
			->save();

		return $order;
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
	public function getSum(): ?float
	{
		return $this->getFieldValue('sum');
	}

	/**
	 * @param float $sum
	 * @return self
	 */
	public function setSum(float $sum): self
	{
		$this->setFieldValue('sum', $sum);
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
		return 'orders';
	}

	public static function createFromArray(array $fields): self
	{
		$client = new self();
		$client
			->setId($fields['id'] ?? null)
			->setClientId($fields['clientId'] ?? null)
			->setSum($fields['sum'] ?? null)
			->setIsPending((bool)$fields['isPending'])
		;

		return $client;
	}
}
