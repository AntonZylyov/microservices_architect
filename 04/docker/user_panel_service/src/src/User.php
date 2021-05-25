<?php

namespace MyApp;

class User
{
	protected static $baseUrl;
	protected $fields = [
		'username',
		'firstName',
		'lastName',
		'email',
		'phone'
	];

	public static function setBaseUrl(string $baseUrl): void
	{
		self::$baseUrl = $baseUrl;
	}

	public function get(int $id)
	{
		return $this->call('/user/' . $id, 'get');
	}

	public function add(array $fields)
	{
		$fields = array_intersect_key($fields, array_flip($this->fields));
		$result = $this->call('/user', 'post', $fields);
		return $result ? $result['id'] : false;
	}

	public function update(int $id, array $fields)
	{
		$fields = array_intersect_key($fields, array_flip($this->fields));
		return $this->call('/user/' . $id, 'put', $fields);
	}

	public function delete(int $id): bool
	{
		return (bool)$this->call('/user/' . $id, 'delete');
	}

	protected function call(string $uri, string $method, array $data = null)
	{
		if (!self::$baseUrl)
		{
			throw new \Exception('No base url!');
		}
		$curl = curl_init(self::$baseUrl . $uri);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, strtoupper($method));
		if (is_array($data))
		{
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
		}
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		$json = curl_exec($curl);
		$result = json_decode($json, true);
		if (is_array($result) && isset($result['error']))
		{
			$result = false;
		}
		return $result;
	}
}