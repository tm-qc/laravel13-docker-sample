<?php

use App\Http\Controllers\Auth\LoginController;
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
| 本番ルート（認証前）
|--------------------------------------------------------------------------
|
*/
// あえてguestつけることで、ログイン済みの時にログイン画面をでなくできる
Route::middleware('guest')->group(function () {
    /*
     * ログイン画面を表示する
     *
     * auth ミドルウェアで未認証と判定された場合、
     * Laravel標準では login という名前付きルート(name('login'))へリダイレクトされる。
     *
     * なので、未認証で適当にアクセスされたときは /login が表示される
     */
    Route::get('/login', [LoginController::class, 'create'])
        ->name('login');

    /*
     * ログイン処理を実行する
     *
     * POST /login
     * メールアドレス・パスワードを送信して認証するため POST を使う。
     */
    Route::post('/login', [LoginController::class, 'authenticate'])
        ->name('login.store')
        ->middleware('throttle:5,1');
});


/*
|--------------------------------------------------------------------------
| 本番ルート（認証後）
|--------------------------------------------------------------------------
|
*/

/*
 * ログイン後に表示する画面
 *
 * auth ミドルウェアを付けることで、
 * 未ログインユーザーはアクセスできないようにする。
 */
Route::middleware('auth')->group(function () {

    // TOP画面
    Route::get('/', [TopController::class, 'index'])
        ->name('top');

    // ログアウト処理を実行する
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');

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
});
