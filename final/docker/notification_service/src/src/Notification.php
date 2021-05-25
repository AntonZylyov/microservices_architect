<?php

namespace NotificationService;

use BaseMicroservice\Database;
use BaseMicroservice\IdempotentEntity;

class Notification extends IdempotentEntity
{
	public static function create(
		string $type,
		array $data,
		string $idempotenceKey = ''
	)
	{
		$notification = new self();
		$notification
			->setType($type)
			->setData($data)
			->setIdempotenceKey($idempotenceKey)
			->save();

		return $notification;
	}

	public static function getLast(): ?self
	{
		$fields = Database::getInstance()->selectOne(static::getTableName(), "1=1", [], ['id' => 'desc']);
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
	 * @return string
	 */
	public function getType(): ?string
	{
		return $this->getFieldValue('type');
	}

	/**
	 * @param string $type
	 * @return self
	 */
	public function setType(string $type): self
	{
		$this->setFieldValue('type', $type);
		return $this;
	}

	/**
	 * @return array
	 */
	public function getData(): ?array
	{
		$rawData = $this->getFieldValue('data');
		return empty($rawData) ? [] : json_decode($rawData, true);
	}

	/**
	 * @param array $data
	 * @return self
	 */
	public function setData($data): self
	{
		$this->setFieldValue('data', is_array($data) ? json_encode($data) : $data);
		return $this;
	}

	public function toArray(): array
	{
		$fields = parent::toArray();
		$fields['data'] = $this->getData();

		return $fields;
	}

	protected static function getTableName(): string
	{
		return 'notifications';
	}

	public static function createFromArray(array $fields): self
	{
		$account = new self();
		$account
			->setId($fields['id'] ?? null)
			->setType($fields['type'] ?? '')
			->setData($fields['data'] ?? [])
		;

		return $account;
	}
}
