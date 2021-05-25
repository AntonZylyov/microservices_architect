<?php

namespace NotificationServiceApp;

class Notification
{
	protected $id = 0;
	protected $type = '';
	protected $data = [];

	private const tableName = 'notifications';

	public static function create(
		string $type,
		array $data
	)
	{
		$notification = new self();
		$notification
			->setType($type)
			->setData($data)
			->save();

		return $notification;
	}

	public static function getById(int $id): ?self
	{

		$fields = Database::getInstance()->selectOne(self::tableName, "id = ?", [$id]);
		if ($fields)
		{
			$notification = new self();
			$notification
				->setId($fields['id'])
				->setType($fields['type'])
				->setData(json_decode($fields['data'], true));
			return $notification;
		}
		return null;
	}

	public static function getLast(): ?self
	{

		$fields = Database::getInstance()->selectOne(self::tableName, "1=1", [], ['id' => 'desc']);
		if ($fields)
		{
			$notification = new self();
			$notification
				->setId($fields['id'])
				->setType($fields['type'])
				->setData(json_decode($fields['data'], true));
			return $notification;
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
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @param string $firstName
	 * @return self
	 */
	public function setType(string $type): self
	{
		$this->type = $type;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getData(): array
	{
		return $this->data;
	}

	/**
	 * @param array $data
	 * @return self
	 */
	public function setData(array $data): self
	{
		$this->data = $data;
		return $this;
	}


	public function save(): void
	{
		$fields = [
			'type' => $this->getType(),
			'data' => json_encode($this->getData())
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