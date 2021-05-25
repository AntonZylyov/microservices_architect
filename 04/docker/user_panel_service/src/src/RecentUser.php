<?php

namespace MyApp;

class RecentUser
{
	private const COOKIE_KEY = 'uids';
	protected $ids = [];

	public function __construct()
	{
		$this->ids = array_key_exists(self::COOKIE_KEY, $_COOKIE) ? explode(',', $_COOKIE[self::COOKIE_KEY]) : [];
	}

	public function getIds(): array
	{
		return $this->ids;
	}

	public function addId(int $id): void
	{
		$this->ids[] = $id;
		$this->save();
	}

	public function removeId(int $id): void
	{
		$pos = array_search($id, $this->ids);
		if (false !== $pos)
		{
			unset($this->ids[$pos]);
			$this->save();
		}
	}

	protected function save(): void
	{
		setcookie(self::COOKIE_KEY, implode(',', $this->ids), time() + 60 * 60 * 24 * 99, '/');
	}
}