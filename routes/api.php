<?php

use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('quiz', [QuizController::class, 'getQuiz'])->name('get.quiz.get');
Route::post('answer', [QuizController::class, 'answer'])->name('post.quiz.answer');
