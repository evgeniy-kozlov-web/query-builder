<?php

namespace app\database;

use app\interfaces\IDatabaseConnection;

abstract class QueryBuilder
{
	protected string $table;

	protected array $bindings = [];
	protected array $placeholders = [];
	protected array $fields = [];

	protected $statement;
	protected $connection;

	protected $type;

	protected const PLACEHOLDER = '?';

	protected const DML_TYPE_CREATE = "INSERT";
	protected const DML_TYPE_READ = "SELECT";
	protected const DML_TYPE_UPDATE = "UPDATE";
	protected const DML_TYPE_DELETE = "DELETE";
	protected const DML_OPERATORS = [
		'=',
		'>',
		'<',
		'>=',
		'<=',
		'<>'
	];

	public function __construct(IDatabaseConnection $connection)
	{
		$this->connection = $connection->connect()->getConnection();
	}

	public function do(): QueryBuilder
	{
		if (empty($this->table)) throw new \app\exceptions\TableIsEmptyException();

		$query = $this->getQuery($this->type);

		$this->prepare($query);
		$this->execute();

		return $this;
	}

	public function table(string $table): QueryBuilder
	{
		if (empty($table)) throw new \app\exceptions\TableIsEmptyException();

		$this->table = $table;

		return $this;
	}

	public function create(array $data): QueryBuilder
	{
		if (empty($this->table)) throw new \app\exceptions\TableIsEmptyException();

		$this->fields = array_keys($data);
		$this->bindings = array_values($data);
		$this->type = self::DML_TYPE_CREATE;

		$this->do();

		return $this;
	}

	public function read(array $fields): QueryBuilder
	{
		if (empty($this->table)) throw new \app\exceptions\TableIsEmptyException();

		$this->fields = $fields;
		$this->type = self::DML_TYPE_READ;

		return $this;
	}

	public function update(array $data): QueryBuilder
	{
		if (empty($this->table)) throw new \app\exceptions\TableIsEmptyException();

		foreach ($data as $key => $value) {
			$this->fields[] = $key . '=' . self::PLACEHOLDER;
			$this->bindings[] = $value;
		}

		$this->type = self::DML_TYPE_UPDATE;

		return $this;
	}

	public function delete(): QueryBuilder
	{
		if (empty($this->table)) throw new \app\exceptions\TableIsEmptyException();

		$this->type = self::DML_TYPE_DELETE;

		return $this;
	}

	public function find(int $id): QueryBuilder
	{
		if (empty($this->table)) throw new \app\exceptions\TableIsEmptyException();

		$this->read(['*'])->where('id', $id)->do();

		return $this;
	}

	public function findBy(string $field, mixed $value): QueryBuilder
	{
		if (empty($this->table)) throw new \app\exceptions\TableIsEmptyException();

		$this->read(['*'])->where($field, $value)->do();

		return $this;
	}

	public function where(string $field, string $value, string $operator = self::DML_OPERATORS[0]): QueryBuilder
	{
		if (!in_array($operator, self::DML_OPERATORS)) throw new \app\exceptions\OperatorIsInvalidException();

		$this->placeholders[] = $field . $operator . self::PLACEHOLDER;
		$this->bindings[] = $value;

		return $this;
	}

	abstract public function prepare(string $query): QueryBuilder;
	abstract public function execute(): QueryBuilder;

	abstract public function get(): array | bool;
	abstract public function first(): array | bool;

	abstract public function count(): int;
	abstract public function lastInsertedId(): int;


	private function getQuery(string $type): string
	{
		switch ($type) {
			case self::DML_TYPE_CREATE:
				return "INSERT INTO " . $this->table . " (" . implode(', ', $this->fields) . ") VALUE (" . rtrim(str_repeat('?, ', count($this->fields)), ', ') . ")";
			case self::DML_TYPE_READ:
				$query = "SELECT " . implode(', ', $this->fields) . ' FROM ' . $this->table;

				if (!empty($this->placeholders)) $query .= ' WHERE ' . implode(' AND ', $this->placeholders);

				return $query;
			case self::DML_TYPE_UPDATE:
				$query = "UPDATE " . $this->table . " SET " . implode(', ', $this->fields);

				if (!empty($this->placeholders)) $query .= ' WHERE ' . implode(' AND ', $this->placeholders);

				return $query;
			case self::DML_TYPE_DELETE:
				$query = "DELETE FROM " . $this->table;

				if (!empty($this->placeholders)) $query .= ' WHERE ' . implode(' AND ', $this->placeholders);

				return $query;
		}

		throw new \app\exceptions\InvalidDMLTypeException();
	}
}
