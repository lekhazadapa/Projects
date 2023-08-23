<?php

class SensorController
{

	public static function index() : string {
		$data = array('carriage' => [], 'platform' => []);

		$carriageSensors = DB::get("*", "carriage_sensors", "", []);
		if ( !is_null($carriageSensors) ) {
			$data['carriage'] = $carriageSensors;
		}

		$platformSensors = DB::get("*", "platform_sensors", "", []);
		if ( !is_null($platformSensors) ) {
			$data['platform'] = $platformSensors;
		}

		return Response::JSON($data);
	}

	public static function get() : string {
		if ( !Request::params()->nonempty('uuid') ) {
			Response::codeBadRequest();
			Response::abort();
		}

		// Investigate for carriage
		$carriageSensor = DB::get("*", "carriage_sensors", "LOWER(uuid) = LOWER(?)", [Request::params()->get('uuid')]);
		if ( !empty($carriageSensor) ) {
			return Response::JSON($carriageSensor[0]);
		}

		// Investigate for platform
		$platformSensor = DB::get("*", "platform_sensors", "LOWER(uuid) = LOWER(?)", [Request::params()->get('uuid')]);
		if ( !empty($platformSensor) ) {
			return Response::JSON($platformSensor[0]);
		}

		Response::codeNotFound();
		Response::abort();
	}

	public static function indexCarriage() : string {
		$carriageSensors = DB::get("*", "carriage_sensors", "", []);
		if ( is_null($carriageSensors) ) {
			Response::codeNotFound();
			Response::abort();
		}
		return Response::JSON($carriageSensors);
	}

	public static function indexPlatform() : string {
		$platformSensor = DB::get("*", "platform_sensors", "", []);
		if ( is_null($platformSensor) ) {
			Response::codeNotFound();
			Response::abort();
		}
		return Response::JSON($platformSensor);
	}

	public static function getPlatform() : string {
		if ( !Request::params()->nonempty('uuid') ) {
			Response::codeBadRequest();
			Response::abort();
		}

		$platformName = DB::get("*", "platforms", "name = (SELECT platform_name FROM platform_sensors WHERE LOWER(uuid) = LOWER(?))", [Request::params()->get('uuid')]);
		if ( empty($platformName) ) {
			Response::codeNotFound();
			Response::abort();
		}
		$platformName = $platformName[0];

		$platformSensors = DB::execute("SELECT * FROM platform_sensors WHERE platform_name = ? ORDER BY position ASC", [$platformName['name']]);
		if ( is_null($platformSensors) ) {
			Response::codeNotFound();
			Response::abort();
		}
		return Response::JSON(array('platform' => $platformName, 'sensors' => $platformSensors));
	}

}