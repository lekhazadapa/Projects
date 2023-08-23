<?php

class CarriageController
{

	public static function index() : string {
		$carriages = DB::get("*", "carriages", "", []);
		if ( is_null($carriages) ) {
			Response::codeNotFound();
			Response::abort();
		}

		return Response::JSON($carriages);
	}

	public static function get() : string {
		if ( !Request::params()->nonempty("id") ) {
			Response::codeBadRequest();
			Response::abort();
		}

		$carriage = DB::get("*", "carriages", "id = ?", [Request::params()->get("id")]);
		if ( is_null($carriage) || empty($carriage) ){
			Response::codeNotFound();
			Response::abort();
		}

		return Response::JSON($carriage[0]);
	}

	public static function indexSensor() : string {
		if ( !Request::params()->nonempty("id") ) {
			Response::codeBadRequest();
			Response::abort();
		}

		$sensors = DB::get("*", "carriage_sensors", "carriage_id = ?", [Request::params()->get('id')]);
		if ( is_null($sensors) || empty($sensors) ) {
			Response::codeNotFound();
			Response::abort();
		}

		return Response::JSON($sensors);
	}

}