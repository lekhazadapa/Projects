<?php
/**
 * @author Robert Englund
 */

class Http {

	private function __construct() {}

	public static function breakUri(string $uri) : array {
		return parse_url($uri);
	}

	public static function buildUri(array $parts) : string {
		return (isset($parts['scheme']) ? "{$parts['scheme']}:" : '') .
			((isset($parts['user']) || isset($parts['host'])) ? '//' : '') .
			(isset($parts['user']) ? "{$parts['user']}" : '') .
			(isset($parts['pass']) ? ":{$parts['pass']}" : '') .
			(isset($parts['user']) ? '@' : '') .
			(isset($parts['host']) ? "{$parts['host']}" : '') .
			(isset($parts['port']) ? ":{$parts['port']}" : '') .
			(isset($parts['path']) ? "{$parts['path']}" : '') .
			(isset($parts['query']) ? "?{$parts['query']}" : '') .
			(isset($parts['fragment']) ? "#{$parts['fragment']}" : '');
	}

}