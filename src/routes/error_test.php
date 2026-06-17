<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| エラー画面確認用ルート
|--------------------------------------------------------------------------
|
| local環境だけで web.php から読み込まれるようにして使っている
|
| 本番環境ではこのファイル自体が読み込まれないため、
| エラー確認用URLが公開されないようになっている。
|
| 確認するときは .env の APP_DEBUG を false(本番) にするとURLで表示確認できます。
|
| Laravel の abort の仕様で本番以外はエラー画面じゃなく、Laravelの開発用エラー詳細画面が表示されます。
| 本番の場合は、errors 配下でHTTPステータスコードと同じ名前のBladeファイルを自動で探す仕組みがあり、それが表示されます。
|
|
| ■エラー画面利用方針
|
| Laravel標準機能で表示するだけの画面を置いておくだけでなので何もしない。
| → 基本これ
|
| エラー画面をサービスやコントローラで自分で直接呼ぶ
| → 基本しない
|   明確にHTTPエラーとして返したいときだけ abort() や findOrFail() を使う
*/



Route::get('/error-test/404', function () {
    // 404 Not Found のエラー画面を確認する
    abort(404);
});

Route::get('/error-test/500', function () {
    // 500 Internal Server Error のエラー画面を確認する
    abort(500);
});

Route::get('/error-test/503', function () {
    // 503 Service Unavailable のエラー画面を確認する
    abort(503);
});

Route::get('/error-test/4xx', function () {
    // 専用のエラー画面がないときに使われる、予備の共通エラー画面
    // サンプルで400で試してるだけ
    // 400.blade.php がない場合、4xx.blade.php を表示する
    abort(400);
});

Route::get('/error-test/5xx', function () {
    // 専用のエラー画面がないときに使われる、予備の共通エラー画面
    // サンプルで501で試してるだけ
    // 501.blade.php がない場合、5xx.blade.php を表示する
    abort(501);
});
