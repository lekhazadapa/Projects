<?php
/**
 * @author Robert Englund
 */

Route::get("/train/", TrainController::class, "index");
Route::get("/train/{id}/", TrainController::class, "get");

Route::get("/carriage/", CarriageController::class, "index");
Route::get("/carriage/{id}/", CarriageController::class, "get");
Route::get("/carriage/{id}/sensor/", CarriageController::class, "indexSensor");

Route::get("/platform/", PlatformController::class, "index");
Route::get("/platform/{name}/", PlatformController::class, "get");
Route::get("/platform/{name}/sensor/", PlatformController::class, "indexSensor");

Route::get("/sensor/", SensorController::class, "index");
Route::get("/sensor/{uuid}/", SensorController::class, "get");
Route::get("/sensor/carriage/", SensorController::class, "indexCarriage");
Route::get("/sensor/platform/", SensorController::class, "indexPlatform");
Route::get("/sensor/platform/{uuid}/", SensorController::class, "getPlatform");

Route::get("/nextTrain/{platform}/", TrainController::class, "next");