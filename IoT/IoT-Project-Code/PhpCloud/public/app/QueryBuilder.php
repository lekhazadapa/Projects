<?php
/**
 * @author Robert Englund
 */

class QueryBuilder
{

	private string $query = "";
	private array $parts = [];

	private function __construct()
	{}

	public static function new() : QueryBuilder
	{
		return new QueryBuilder();
	}

	public function build() : string
	{
		$this->addIfPresent('EXISTS_START');

		$this->addIfPresent('SELECT');
		$this->addIfPresent('FROM');
		$this->addIfPresent('UPDATE');
		$this->addIfPresent('SET');
		$this->addIfPresent('DELETE');
		$this->addIfPresent('WHERE');
		$this->addIfPresent("ORDER");
		$this->addIfPresent("LIMIT");
		$this->addIfPresent("OFFSET");

		$this->addIfPresent('EXISTS_END');

		$this->addIfPresent('INSERT INTO');
		$this->addIfPresent('VALUES');

		return $this->query . ";";
	}

	private function addIfPresent(string $key) : void
	{
		$this->query .= isset($this->parts[$key])
			? $this->parts[$key] . " "
			: "" ;
	}

	public function select(string $selector) : QueryBuilder
	{
		if ( empty($selector) ) {
			return $this;
		}
		$this->parts['SELECT'] = "SELECT ".$selector;
		return $this;
	}

	public function from(string $tables) : QueryBuilder
	{
		if ( empty($tables) ) {
			return $this;
		}
		$this->parts['FROM'] = "FROM ".$tables;
		return $this;
	}

	public function where(string $condition) : QueryBuilder
	{
		if ( empty($condition) ) {
			return $this;
		}
		$this->parts['WHERE'] = "WHERE ".$condition;
		return $this;
	}

	public function order(string $order) : QueryBuilder
	{
		if ( empty($order) ) {
			return $this;
		}
		$this->parts['ORDER'] = "ORDER BY ".$order;
		return $this;
	}

	public function limit(string $limit) : QueryBuilder
	{
		if ( empty($limit) ) {
			return $this;
		}
		$this->parts['LIMIT'] = "LIMIT ".$limit;
		return $this;
	}

	public function offset(string $offset) : QueryBuilder
	{
		if ( empty($offset) ) {
			return $this;
		}
		$this->parts['OFFSET'] = "OFFSET ".$offset;
		return $this;
	}

	public function insert(string $table) : QueryBuilder
	{
		if ( empty($table) ) {
			return $this;
		}
		$this->parts['INSERT INTO'] = "INSERT INTO ".$table;
		return $this;
	}

	public function values(array $values) : QueryBuilder
	{
		if ( empty($values) ) {
			return $this;
		}
		$questionMarks = "";
		$properties = "";
		foreach ( $values as $item ) {
			$questionMarks .= "?,";
			$properties .= $item . ",";
		}
		$questionMarks = substr_replace($questionMarks, "", -1);
		$properties = substr_replace($properties, "", -1);
		$this->parts['VALUES'] = "(".$properties.") VALUES (".$questionMarks.")";
		return $this;
	}

	public function update(string $table) : QueryBuilder
	{
		if ( empty($table) ) {
			return $this;
		}
		$this->parts['UPDATE'] = "UPDATE ".$table;
		return $this;
	}

	public function set(array $values) : QueryBuilder
	{
		if ( empty($values) ) {
			return $this;
		}
		$properties = "";
		foreach ( $values as $item ) {
			$properties .= $item . " = ?,";
		}
		$properties = substr_replace($properties, "", -1);
		$this->parts['SET'] = "SET ".$properties;
		return $this;
	}

	public function delete(string $table) : QueryBuilder
	{
		if ( empty($table) ) {
			return $this;
		}
		$this->parts['DELETE'] = "DELETE FROM ".$table;
		return $this;
	}

	public function exists() : QueryBuilder
	{
		$this->parts['EXISTS_START'] = "SELECT EXISTS (";
		$this->parts['EXISTS_END'] = ") AS 'EXISTS'";
		return $this;
	}

}