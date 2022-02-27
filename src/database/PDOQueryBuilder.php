<?php

namespace app\database;

class PDOQueryBuilder extends QueryBuilder
{
	protected const FETCH_TYPE = \PDO::FETCH_ASSOC;

	public function __construct(PDOConnection $connection)
	{
		parent::__construct($connection);
	}

	public function prepare(string $query): PDOQueryBuilder
	{
		$this->statement = $this->connection->prepare($query);

		return $this;
	}

	public function execute(): PDOQueryBuilder
	{
		$this->statement->execute($this->bindings);

		$this->placeholders = [];
		$this->bindings = [];
		$this->fields = [];

		return $this;
	}

	public function get(): array | bool
	{
		return $this->statement->fetchAll(self::FETCH_TYPE);
	}

	public function first(): array | bool
	{
		return $this->statement->fetch(self::FETCH_TYPE);
	}

	public function count(): int
	{
		return $this->statement->rowCount();
	}

	public function lastInsertedId(): int
	{
		return $this->connection->lastInsertId();
	}
}
