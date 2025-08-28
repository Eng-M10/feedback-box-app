<?php

use App\Http\Controllers\SentimentoController;
use App\Http\Controllers\TranslateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/analisar', [SentimentoController::class, 'analisar']);
Route::get('/estatistica',[SentimentoController::class, 'estatistica']);

Route::post('/test' , function(Request $request) {
        $message = $request->json("message");

        $obj = new SentimentoController();

        $result = $obj->analyzeSentiment($message);

        return response()->json(['object'=>$result]);
});
