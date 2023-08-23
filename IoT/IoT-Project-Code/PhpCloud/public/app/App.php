<?php
/**
 * @author Robert Englund
 */

class App {

	private function __construct() {}

	public static function registerRoutes() : void {
		require(Path::system() . "routes/api.php");
	}

	public static function handleRequest(): void {
		$uri = $_SERVER['REQUEST_URI'];
		$method = mb_strtoupper($_SERVER['REQUEST_METHOD']);
		echo Route::dispatch($uri, $method);
	}

}