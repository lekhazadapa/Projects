<?php
/**
 * @author Robert Englund
 */

class DB
{

	private static string $serverip = "localhost";
	private static string $username = "root";
	private static string $password = "";
	private static string $database = "iot";

	private static object $connection;

	private static bool $open = false;

	private function __construct()
	{}

	public static function open() : void
	{
		if ( self::$open ) {
			return;
		}
		self::$connection = new mysqli( self::$serverip , self::$username , self::$password , self::$database );
		if ( self::$connection->connect_error ) {
			die();
		}
		self::$connection->set_charset('utf8mb4');
		self::$connection->query("SET NAMES utf8mb4");
		self::$open = true;
	}

	public static function close() : void
	{
		if ( !self::$open ) {
			return;
		}
		self::$connection->close();
		self::$open = false;
	}

	public static function transaction() : void
	{
		self::$connection->begin_transaction();
	}
	public static function commit() : void
	{
		self::$connection->commit();
	}

	public static function prepare($string) : object
	{
		self::open();

		return self::$connection->prepare($string);
	}

	public static function exists(string $tables, string $conditions, array $params) : bool|null
	{
		$query = QueryBuilder::new()->exists()->select("*")->from($tables)->where($conditions)->build();
		$stmt = DB::prepare($query);
		if (!$stmt) {
			return null;
		}
		$stmt->execute($params);

		$result = $stmt->get_result();
		$stmt->close();

		return $result->fetch_all(MYSQLI_ASSOC)[0]['EXISTS'] == 1;
	}

	public static function get(string $targets, string $tables, string $conditions, array $params) : array|null
	{
		$query = QueryBuilder::new()->select($targets)->from($tables)->where($conditions)->build();
		return self::execute($query, $params);
	}

	public static function execute(string $query, array $params) : array|null
	{
		$stmt = DB::prepare($query);
		if (!$stmt) {
			return null;
		}
		$stmt->execute($params);

		$result = $stmt->get_result();
		$stmt->close();

		return $result->fetch_all(MYSQLI_ASSOC);
	}

	public static function count(string $tables, string $conditions, array $params) : int|null
	{
		$query = QueryBuilder::new()->select("COUNT(*)")->from($tables)->where($conditions)->build();
		$stmt = DB::prepare($query);
		if (!$stmt) {
			return null;
		}
		$stmt->execute($params);

		$result = $stmt->get_result();
		$stmt->close();

		return $result->fetch_all(MYSQLI_ASSOC)[0]['COUNT(*)'];
	}

	public static function insert(string $table, array $values, array $params) : int|string|null
	{
		$query = QueryBuilder::new()->insert($table)->values($values)->build();
		$stmt = DB::prepare($query);
		if (!$stmt) {
			return null;
		}
		$stmt->execute($params);
		$insertedId = $stmt->insert_id;
		$stmt->close();

		return $insertedId;
	}

	public static function update(string $table, array $values, string $conditions, array $params) : bool|null
	{
		$query = QueryBuilder::new()->update($table)->set($values)->where($conditions)->build();
		$stmt = DB::prepare($query);
		if (!$stmt) {
			return null;
		}
		$stmt->execute($params);
		$stmt->close();

		return true;
	}

	public static function remove(string $table, string $conditions, array $params) : bool|null
	{
		$query = QueryBuilder::new()->delete($table)->where($conditions)->build();
		$stmt = DB::prepare($query);
		if (!$stmt) {
			return null;
		}
		$stmt->execute($params);
		$stmt->close();

		return true;
	}

	public static function hasSqlStatementResponse($result) : bool {
		return $result->num_rows > 0;
	}

}