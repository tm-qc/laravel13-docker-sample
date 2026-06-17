<?php

use App\Http\Controllers\TopController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

/*
|--------------------------------------------------------------------------
| ローカル確認用ルート
|--------------------------------------------------------------------------
|
| エラー画面確認用のルートは本番環境では不要。
| そのため local 環境だけ別ファイルを読み込む。
|
*/
if (app()->environment('local')) {
    require __DIR__ . '/error_test.php';
}

/*
|--------------------------------------------------------------------------
| 本番ルート
|--------------------------------------------------------------------------
|
*/

// Laravelのトップページ
Route::get('/', [TopController::class, 'index'])
    ->name('top');


/**
 * TOP画面
 */
Route::view('/', 'top')->name('top');

/**
 * ユーザーCRUDのルートをリソースで一括定義
 *
 * index   GET    /users
 * create  GET    /users/create
 * store   POST   /users
 * show    GET    /users/{user}
 * edit    GET    /users/{user}/edit
 * update  PUT    /users/{user}
 * destroy DELETE /users/{user}
 *
 * 確認
 * php artisan route:list
 */
Route::resource('users', UserController::class);

// ユーザーを論理削除する
Route::delete('/users/{user}/soft-delete', [UserController::class, 'softDestroy'])
    ->name('users.soft-destroy');

// ユーザを物理削除する
Route::delete('/users/{user}/force-delete', [UserController::class, 'forceDestroy'])
    ->name('users.force-destroy');
