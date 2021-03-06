<?php

namespace app\database;

use app\interfaces\IDatabaseConnection;

abstract class QueryBuilder
{
	private string $table;

	private array $bindings = [];
	private array $placeholders = [];
	private array $fields = [];

	protected $statement;
	protected $connection;

	private $type;

	private const PLACEHOLDER = '?';

	private const DML_TYPE_CREATE = "INSERT";
	private const DML_TYPE_READ = "SELECT";
	private const DML_TYPE_UPDATE = "UPDATE";
	private const DML_TYPE_DELETE = "DELETE";
	private const DML_OPERATORS = [
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

		$this->placeholders = [];
		$this->bindings = [];
		$this->fields = [];

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
		$this->fields = array_keys($data);
		$this->bindings = array_values($data);
		$this->type = self::DML_TYPE_CREATE;

		$this->do();

		return $this;
	}

	public function read(array $fields): QueryBuilder
	{
		$this->fields = $fields;
		$this->type = self::DML_TYPE_READ;

		return $this;
	}

	public function update(array $data): QueryBuilder
	{
		foreach ($data as $key => $value) {
			$this->fields[] = $key . '=' . self::PLACEHOLDER;
			$this->bindings[] = $value;
		}

		$this->type = self::DML_TYPE_UPDATE;

		return $this;
	}

	public function delete(): QueryBuilder
	{
		$this->type = self::DML_TYPE_DELETE;

		return $this;
	}

	public function find(int $id): QueryBuilder
	{
		$this->read(['*'])->where('id', $id)->do();

		return $this;
	}

	public function findBy(string $field, mixed $value): QueryBuilder
	{
		$this->read(['*'])->where($field, $value)->do();

		return $this;
	}

	public function where(string $field, string $value, string $operator = self::DML_OPERATORS[0]): QueryBuilder
	{
		if (!in_array($operator, self::DML_OPERATORS)) throw new \app\exceptions\InvalidOperatorException();

		$this->placeholders[] = $field . $operator . self::PLACEHOLDER;
		$this->bindings[] = $value;

		return $this;
	}

	abstract protected function prepare(string $query): QueryBuilder;
	abstract protected function execute(): QueryBuilder;

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
