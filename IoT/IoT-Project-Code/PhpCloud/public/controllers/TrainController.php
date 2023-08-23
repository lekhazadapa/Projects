<?php
/**
 * @author Robert Englund
 */

 class TrainController {

    public static function index() : string {
        $trains = DB::get("*", "trains", "", []);
        if ( is_null($trains) ) {
            Response::codeNotFound();
            Response::abort();
        }

        return Response::JSON($trains);
    }

	public static function get() : string {
		if ( !Request::params()->nonempty("id") ) {
			Response::codeBadRequest();
			Response::abort();
		}

		$train = DB::get("*", "trains", "id = ?", [Request::params()->get("id")]);
		if ( is_null($train) || empty($train) ){
			Response::codeNotFound();
			Response::abort();
		}

		return Response::JSON($train[0]);
	}

	public static function next() : string {
		if ( !Request::params()->nonempty("platform") ) {
			Response::codeBadRequest();
			Response::abort();
		}
		$simulatedTrainId = 1;

		$train = DB::get("*", "trains", "id = ?", [$simulatedTrainId]);
		if ( is_null($train) || empty($train) ) {
			Response::codeNotFound();
			Response::abort();
		}

		$carriages = DB::execute("SELECT * FROM carriages WHERE train_id = ? ORDER BY position ASC", [$simulatedTrainId]);

		return Response::JSON(array('train' => $train[0], 'carriages' => $carriages));
	}

 }