<?php
/**
 * @author Robert Englund
 */

class Route {

	private string $method;
	private string $uri;
	private string $controller;
	private string $function;
	private array $parameters;

	private function __construct() {}

	private static array $routes = [
		'GET' => [],
		'POST' => [],
		'PUT' => [],
		'PATCH' => [],
		'DELETE' => []
	];

	private static function assign($uri, $method, $controller, $function): Route {
		$uri = Http::breakUri($uri);
		if ( mb_substr($uri['path'], "-1") !== "/" ) {
			$uri['path'] .= "/";
		}
		$uri = Http::buildUri($uri);

		$route = new Route();
		$route->uri = $uri;
		$route->method = $method;
		$route->controller = $controller;
		$route->function = $function;

		$dynamicRoute = preg_match("/{\w+}/", $uri);

		if ($dynamicRoute) {
			self::$routes[$method][] = $route;
		}
		else {
			array_unshift(self::$routes[$method], $route);
		}

		return $route;
	}

	public static function get($path, $controller, $function) : Route {
		return self::assign($path, "GET", $controller, $function);
	}
	public static function post($path, $controller, $function) : Route {
		return self::assign($path, "POST", $controller, $function);
	}
	public static function put($path, $controller, $function) : Route {
		return self::assign($path, "PUT", $controller, $function);
	}
	public static function patch($path, $controller, $function) : Route {
		return self::assign($path, "PATCH", $controller, $function);
	}
	public static function delete($path, $controller, $function) : Route {
		return self::assign($path, "DELETE", $controller, $function);
	}

	public static function dispatch($uri, $method) : string {

		$uri = Http::breakUri($uri);
		foreach ($uri as &$part) {
			$part = urldecode($part);
		}
		if ( mb_substr($uri['path'], "-1") !== "/" ) {
			$uri['path'] .= "/";
			$uri = Http::buildUri($uri);
			Response::redirect($uri);
		}

		$routes = self::$routes[$method];
		$selectedRoute = self::findRoute($routes, $uri);

		if ( is_null($selectedRoute) ) {
			Response::codeNotFound();
			Response::abort();
		}

		return Dispatcher::process($selectedRoute);
	}

	private static function findRoute($routes, $uri) : Route|null {
		$selectedRoute = null;
		$parameters = array();

		$uri_parts = explode("/", $uri['path']);

		foreach ( $routes as $route ) {
			$route_parts = explode("/", $route->uri);
			if ( count($route_parts) != count($uri_parts) ) {
				continue;
			}
			$found = true;
			for ($i = 0; $i < count($uri_parts); $i++) {
				if ( $uri_parts[$i] == $route_parts[$i] ) {
					continue;
				}
				$parameter = preg_match("/{\w+}/", $route_parts[$i]);
				if ( $parameter ) {
					$variable = mb_substr($route_parts[$i], 1, mb_strlen($route_parts[$i])-2);
					$parameters[$variable] = $uri_parts[$i];
					continue;
				}
				$found = false;
				break;
			}
			if ($found) {
				$selectedRoute = $route;
				break;
			}
		}

		$selectedRoute->setParameters($parameters);
		return $selectedRoute;
	}

	public function getMethod(): string
	{
		return $this->method;
	}

	public function getUri(): string
	{
		return $this->uri;
	}

	public function getController(): string
	{
		return $this->controller;
	}

	public function getFunction(): string
	{
		return $this->function;
	}

	public function getParameters() : array {
		return $this->parameters;
	}

	public function setParameters(array $params) : void {
		$this->parameters = $params;
	}

}