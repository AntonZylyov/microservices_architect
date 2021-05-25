<?php

namespace NotificationServiceApp;


class UserService
{
	protected $host = '';
	public function __construct()
	{
		$config = \NotificationServiceApp\Config::getInstance();
		$this->host = $config->getUserServiceHost();
	}
	public function getById(int $userId): array
	{
		$client = new \GuzzleHttp\Client();
		$response = $client->request(
			'GET',
			$this->host.'/user/'.$userId
		);
		$data = json_decode($response->getBody()->getContents(), true);
		if (!is_array($data))
		{
			$data = [
				'error' => 'Не удалось получить данные пользователя'
			];
		}
		return $data;
	}
}