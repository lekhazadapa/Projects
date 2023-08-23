<?php
/**
 * @author Robert Englund
 */

class Data
{

	private array $data;

	public function __construct(array $data)
	{
		$this->data = $this->sanitizeArray($data);
	}

	public function all() : array
	{
		return $this->data;
	}

	public function exists(string|int $key) : bool
	{
		return isset($this->data[$key]);
	}

	public function nonempty(string|int $key) : bool
	{
		return $this->exists($key) && trim($this->data[$key]) !== "";
	}

	public function empty(string|int $key) : bool
	{
		return !$this->nonempty($key);
	}

	public function get(string|int $key) : mixed
	{
		return $this->empty($key) ? null : $this->data[$key];
	}

	private function sanitizeArray(array $array) : array
	{
		$keys = array_keys($array);
		$size = sizeOf($keys);
		for ($i = 0; $i < $size; $i++) {
			$key = $keys[$i];
			if ( is_string($array[$key]) ) {
				$array[$key] = $this->sanitizeEntry($array[$key]);
			}
			else if ( is_array($array[$key]) ) {
				$array[$key] = $this->sanitizeArray($array[$key]);
			}
		}
		return $array;
	}
	private function sanitizeEntry(string $data) : string
	{
		return trim($data);
	}

}