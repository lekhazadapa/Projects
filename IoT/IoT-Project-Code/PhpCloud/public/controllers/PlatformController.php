<?php

class PlatformController
{

	public static function index() : string {
		$platforms = DB::get("*", "platforms", "", []);
		if ( is_null($platforms) ) {
			Response::codeNotFound();
			Response::abort();
		}

		return Response::JSON($platforms);
	}

	public static function get() : string {
		if ( !Request::params()->nonempty("name") ) {
			Response::codeBadRequest();
			Response::abort();
		}

		$platform = DB::get("*", "platforms", "name = ?", [Request::params()->get("name")]);
		if ( is_null($platform) || empty($platform) ){
			Response::codeNotFound();
			Response::abort();
		}

		return Response::JSON($platform[0]);
	}

	public static function indexSensor() : string {
		if ( !Request::params()->nonempty("name") ) {
			Response::codeBadRequest();
			Response::abort();
		}

		$sensors = DB::get("*", "platform_sensors", "platform_name = ?", [Request::params()->get('name')]);
		if ( is_null($sensors) || empty($sensors) ) {
			Response::codeNotFound();
			Response::abort();
		}

		return Response::JSON($sensors);
	}

}