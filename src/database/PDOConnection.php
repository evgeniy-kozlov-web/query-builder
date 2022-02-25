<?php

namespace app\database;

class PDOConnection extends AbstractConnection implements \app\interfaces\IDatabaseConnection
{
	protected const REQUIRED_KEYS = [
		'driver',
		'db_name',
		'host',
		'username',
		'password'
	];

	public function connect(): PDOConnection
	{
		$this->connection = new \PDO($this->parseCredentials(), $this->credentials['username'], $this->credentials['password']);

		return $this;
	}

	public function getConnection(): \PDO
	{
		return $this->connection;
	}

	private function parseCredentials()
	{
		return $this->credentials['driver'] . ':dbname=' . $this->credentials['db_name'] . ';host=' . $this->credentials['host'];
	}
}
