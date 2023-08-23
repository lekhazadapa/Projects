<?php
/**
 * @author Robert Englund
 */

class Response
{
	private function __construct()
	{}

	public static function redirect($destination) : never
	{
		header("Location: " . $destination);
		self::codeRedirect();
		self::abort();
	}

	public static function abort() : never
	{
		exit();
	}

	public static function JSON(array $json) : string
	{
		self::contentTypeJSON();
		self::codeOK();
		return json_encode((new Data($json))->all(), JSON_UNESCAPED_UNICODE);
	}

	public static function escape($data) : string
	{
		return htmlspecialchars($data);
	}

	public static function codeOK() : void
	{
		http_response_code(200);
	}

	public static function codeMoved() : void
	{
		http_response_code(301);
	}

	public static function codeRedirect() : void
	{
		http_response_code(302);
	}

	public static function codeBadRequest() : void
	{
		http_response_code(400);
	}

	public static function codeConflict() : void
	{
		http_response_code(409);
	}

	public static function codeInternalError() : void
	{
		http_response_code(500);
	}

	public static function codeNotFound() : void
	{
		http_response_code(404);
	}

	public static function codeWrongMethod() : void
	{
		http_response_code(405);
	}

	public static function codeUnauthorized() : void
	{
		http_response_code(401);
	}

	public static function codeForbidden() : void
	{
		http_response_code(403);
	}

	public static function contentTypeHTML() : void
	{
		header('Content-Type: text/html; charset=UTF-8');
	}

	public static function contentTypeText() : void
	{
		header('Content-Type: text/plain; charset=UTF-8');
	}

	public static function contentTypeJSON() : void
	{
		header("Content-Type: application/json; charset=UTF-8");
	}
}