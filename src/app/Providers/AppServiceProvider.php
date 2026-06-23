<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // アプリ起動時にサービスコンテナへの登録(バインド)を行う

        // ※ 今回は省略してるインターフェースを使う場合の例
        // $this->app->bind(
        //     UserRepositoryInterface::class,
        //     UserRepository::class
        // );

    }
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         // アプリケーション起動時の共通設定を行う

        // サービスプロバイダが登録された後に行いたい処理などをかく
        /**
         * 本番環境ではURL生成をHTTPSに固定する。
         *
         * ロードバランサーやリバースプロキシ配下で、
         * http URL が生成される問題を防ぐために使うことがある。
         */
        // if (app()->environment('production')) {
        //     URL::forceScheme('https');
        // }
    }
}
