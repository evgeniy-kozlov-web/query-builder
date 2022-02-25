<?php

namespace app\database;

abstract class AbstractConnection
{
	protected array $credentials;
	protected $connection;

	protected const REQUIRED_KEYS = [];

	public function __construct(array $credentials)
	{
		if (!$this->credentialsHasRequiredKeys($credentials)) throw new \app\exceptions\BadCredentialsException();

		$this->credentials = $credentials;
	}

	private function credentialsHasRequiredKeys(array $credentials): bool
	{
		$credentialsKeys = array_keys($credentials);

		$credentialsKeys = array_uintersect(static::REQUIRED_KEYS, $credentialsKeys, "strcasecmp");

		return count(static::REQUIRED_KEYS) == count($credentialsKeys);
	}
}
